<?php

declare(strict_types=1);

namespace KanyJoz\Sessions;

use KanyJoz\Sessions\Exception\SessionException;
use KanyJoz\Sessions\Exception\SessionValidationException;
use KanyJoz\Sessions\Interface\FlashInterface;
use KanyJoz\Sessions\Interface\SessionInterface;
use KanyJoz\Sessions\Interface\SessionManagerInterface;

readonly class Session implements SessionInterface, SessionManagerInterface, FlashInterface
{
    /**
     * @throws SessionValidationException
     */
    public function __construct(private SessionConfig $options, private string $flashKey = '_flash_messages')
    {
        $v = new Validator();
        $v->check($v->permitted($options->getSamesite(), ['Strict', 'Lax']), 'samesite', 'Can only be set to "Strict" or "Lax"');
        $v->check($v->matches($options->getName(), Validator::ALPHA), 'name', 'Must only contain alphanumerical characters');
        $v->check($v->between($options->getSidLength(), 22, 256), 'sid_length', 'Must be between 22 and 256');
        $v->check($v->permitted($options->getSidBitsPerCharacter(), [4, 5, 6]), 'sid_bits_per_character', 'Can only be 4, 5 or 6');
        $v->check($v->permitted($options->getCacheLimiter(), ['nocache', 'private', 'private_no_expire', 'public']), 'cache_limiter', 'Can only be one of the following values: "nocache", "private", "private_no_expire", "public"');

        if (!$v->isValid()) {
            $option = array_key_first($v->errors());
            $error = $v->errors()[$option];
            throw new SessionValidationException($option . ' -> ' . $error);
        }
    }

    // SessionManagerInterface implementation
    public function start(): void
    {
        if ($this->isStarted()) {
            throw new SessionException('Failed to start the session: Already started.');
        }

        if (headers_sent($file, $line)) {
            throw new SessionException(
                sprintf(
                    'Failed to start the session: Headers have already been sent by "%s" at line %d.',
                    $file,
                    $line
                )
            );
        }

        $sessionCookieOptions = [
            'lifetime' => $this->options->getLifetime(),
            'path' => $this->options->getPath(),
            'domain' => $this->options->getDomain(),
            'secure' => $this->options->isSecure(),
            'httponly' => $this->options->isHttponly(),
            'samesite' => $this->options->getSamesite(),
        ];

        if (!session_set_cookie_params($sessionCookieOptions)) {
            throw new SessionException('Failed to start the session: Could not set session cookie.');
        }

        $sessionOptions = [
            'name' => $this->options->getName(),
            'sid_length' => $this->options->getSidLength(),
            'sid_bits_per_character' => $this->options->getSidBitsPerCharacter(),
            'use_strict_mode' => $this->options->isUseStrictMode(),
            'cache_limiter' => $this->options->getCacheLimiter(),
            'referer_check' => $this->options->getRefererCheck(),
        ];

        if (!session_start($sessionOptions)) {
            throw new SessionException('Failed to start the session.');
        }

        // Set empty array for flash messages
        if (!isset($_SESSION[$this->flashKey])) {
            $_SESSION[$this->flashKey] = [];
        }
    }

    public function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function regenerateId(): void
    {
        if (!$this->isStarted()) {
            throw new SessionException('Failed to regenerate session id: Session is not started yet.');
        }

        if (headers_sent($file, $line)) {
            throw new SessionException(
                sprintf(
                    'Failed to start the session: Headers have already been sent by "%s" at line %d.',
                    $file,
                    $line
                )
            );
        }

        if (!session_regenerate_id(true)) {
            throw new SessionException('Failed to regenerate session id.');
        }
    }

    public function destroy(): void
    {
        if (!$this->isStarted()) {
            return;
        }

        $this->clear();

        setcookie(
            $this->options->getName(),
            '',
            [
                'expires' => time() - 7200,
                'path' => $this->options->getPath(),
                'domain' => $this->options->getDomain(),
                'secure' => $this->options->isSecure(),
                'httponly' => $this->options->isHttponly(),
                'samesite' => $this->options->getSamesite(),
            ],
        );

        if (session_unset() === false) {
            throw new SessionException('Failed to unset session.');
        }

        if (session_destroy() === false) {
            throw new SessionException('Failed to destroy session.');
        }
    }

    public function getId(): string
    {
        return (string)session_id();
    }

    public function getName(): string
    {
        return $this->options->getName();
    }

    public function save(): bool
    {
        return session_write_close();
    }

    // SessionInterface implementation
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function clear(): void
    {
        $keys = array_keys($_SESSION);
        foreach ($keys as $key) {
            unset($_SESSION[$key]);
        }
    }

    // FlashInterface implementation
    public function flashGet(string $key): ?string
    {
        if (!$this->flashHas($key)) {
            return null;
        }

        $return = $_SESSION[$this->flashKey][$key];
        unset($_SESSION[$this->flashKey][$key]);

        return (string)$return;
    }

    public function flashPut(string $key, string $message): void
    {
        $_SESSION[$this->flashKey][$key] = $message;
    }

    public function flashHas(string $key): bool
    {
        return array_key_exists($key, $_SESSION[$this->flashKey]);
    }

    public function flashClear(): void
    {
        $keys = array_keys($_SESSION[$this->flashKey]);
        foreach ($keys as $key) {
            unset($_SESSION[$this->flashKey][$key]);
        }
    }
}
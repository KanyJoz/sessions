<?php

declare(strict_types=1);

namespace KanyJoz\Sessions;

class SessionConfig
{
    // Session Cookie Config
    private int $lifetime = 3600; // 1 hr
    private string $path = '/';
    private string $domain = '';
    private bool $secure = false; // Should be true for production with HTTPS connection
    private bool $httponly = true;
    private string $samesite = 'Lax'; // Should be Strict in production

    // Session Config
    private string $name = 'PHPSESSID';
    private int $sid_length = 32; // Should be 96 in production
    private int $sid_bits_per_character = 4; // Should be 6 in production
    private bool $use_strict_mode = false; // Should be true in production
    private string $cache_limiter = 'nocache';
    private string $referer_check = ''; // Should be set if domain is set

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function setLifetime(int $lifetime): SessionConfig
    {
        $this->lifetime = $lifetime;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): SessionConfig
    {
        $this->path = $path;
        return $this;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): SessionConfig
    {
        $this->domain = $domain;
        return $this;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    public function setSecure(bool $secure): SessionConfig
    {
        $this->secure = $secure;
        return $this;
    }

    public function isHttponly(): bool
    {
        return $this->httponly;
    }

    public function setHttponly(bool $httponly): SessionConfig
    {
        $this->httponly = $httponly;
        return $this;
    }

    public function getSamesite(): string
    {
        return $this->samesite;
    }

    public function setSamesite(string $samesite): SessionConfig
    {
        $this->samesite = $samesite;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): SessionConfig
    {
        $this->name = $name;
        return $this;
    }

    public function getSidLength(): int
    {
        return $this->sid_length;
    }

    public function setSidLength(int $sid_length): SessionConfig
    {
        $this->sid_length = $sid_length;
        return $this;
    }

    public function getSidBitsPerCharacter(): int
    {
        return $this->sid_bits_per_character;
    }

    public function setSidBitsPerCharacter(int $sid_bits_per_character): SessionConfig
    {
        $this->sid_bits_per_character = $sid_bits_per_character;
        return $this;
    }

    public function isUseStrictMode(): bool
    {
        return $this->use_strict_mode;
    }

    public function setUseStrictMode(bool $use_strict_mode): SessionConfig
    {
        $this->use_strict_mode = $use_strict_mode;
        return $this;
    }

    public function getCacheLimiter(): string
    {
        return $this->cache_limiter;
    }

    public function setCacheLimiter(string $cache_limiter): SessionConfig
    {
        $this->cache_limiter = $cache_limiter;
        return $this;
    }

    public function getRefererCheck(): string
    {
        return $this->referer_check;
    }

    public function setRefererCheck(string $referer_check): SessionConfig
    {
        $this->referer_check = $referer_check;
        return $this;
    }
}
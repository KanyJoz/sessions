<?php

declare(strict_types=1);

namespace KanyJoz\Sessions;

class Validator
{
    public const string ALPHA = '/^[a-zA-Z]+[a-zA-Z0-9._]+$/';

    private array $errors = [];

    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    public function addError(string $key, string $msg): void
    {
        if (!array_key_exists($key, $this->errors)) {
            $this->errors[$key] = $msg;
        }
    }

    public function check(bool $ok, string $key, string $msg): void
    {
        if (!$ok) {
            $this->addError($key, $msg);
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    // Pure validators
    public function permitted(mixed $value, array $in): bool
    {
        foreach ($in as $element) {
            if ($value === $element) {
                return true;
            }
        }

        return false;
    }

    public function matches(string $value, string $pattern): bool
    {
        return (bool)preg_match($pattern, $value);
    }

    public function between(int $value, int $minInclusive, int $maxInclusive): bool
    {
        return $minInclusive <= $value && $maxInclusive >= $value;
    }
}
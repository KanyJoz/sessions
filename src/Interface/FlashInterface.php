<?php

declare(strict_types=1);

namespace KanyJoz\Sessions\Interface;

/**
 * Adds flash message functionality.
 */
interface FlashInterface
{
    /**
     * Gets a flash message from the session by key.
     * After retrieval the value is unset from the session.
     *
     * @param string $key
     *
     * @return string|null The flash message if key is found. Returns null otherwise.
     */
    public function flashGet(string $key): ?string;

    /**
     * Puts a flash message into the session by key.
     *
     * @param string $key
     * @param string $message
     *
     * @return void
     */
    public function flashPut(string $key, string $message): void;

    /**
     * Check if a flash message key exists in the session.
     * Does not unset the flash message from the session.
     *
     * @param string $key
     *
     * @return bool True if the key exists, false if not.
     */
    public function flashHas(string $key): bool;

    /**
     * Clear all flash message entries from the session.
     *
     * @return void
     */
    public function flashClear(): void;
}
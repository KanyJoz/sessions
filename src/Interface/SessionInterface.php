<?php

declare(strict_types=1);

namespace KanyJoz\Sessions\Interface;

/**
 * The session data operations.
 */
interface SessionInterface
{
    /**
     * Gets a session entry by key.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed The session entry value if key is found. Returns default value otherwise
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Puts a session entry by key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function put(string $key, mixed $value): void;

    /**
     * Check if an entry key exists in the session.
     *
     * @param string $key
     *
     * @return bool True if the key exists, false if not.
     */
    public function has(string $key): bool;

    /**
     * Deletes an entry in the session by key.
     *
     * @param string $key
     *
     * @return void
     */
    public function delete(string $key): void;

    /**
     * Clear all session entries.
     *
     * @return void
     */
    public function clear(): void;
}
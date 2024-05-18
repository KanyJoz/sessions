<?php

declare(strict_types=1);

namespace KanyJoz\Sessions\Interface;

use KanyJoz\Sessions\Exception\SessionException;

/**
 * Manages the session itself.
 */
interface SessionManagerInterface
{
    /**
     * Starts the session.
     *
     * @return void
     *
     * @throws SessionException
     */
    public function start(): void;

    /**
     * Checks if the session was started.
     *
     * @return bool
     */
    public function isStarted(): bool;

    /**
     * Regenerates session id while maintaining all session entries.
     *
     * @return void
     *
     * @throws SessionException
     */
    public function regenerateId(): void;

    /**
     * Clears all session data, destroys the session and deletes the session cookie.
     *
     * @return void
     *
     * @throws SessionException
     */
    public function destroy(): void;

    /**
     * Returns the generated session ID as string.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns the session name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Force the session to be saved and closed.
     *
     * @return bool True on success, false on failure.
     */
    public function save(): bool;
}
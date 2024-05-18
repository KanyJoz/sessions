<?php

namespace KanyJoz\Session\Test\Middleware;

use KanyJoz\Sessions\Exception\SessionValidationException;
use KanyJoz\Sessions\Interface\SessionManagerInterface;
use KanyJoz\Sessions\Middleware\SessionStartMiddleware;
use KanyJoz\Sessions\Session;
use KanyJoz\Sessions\SessionConfig;
use Middlewares\Utils\Dispatcher;
use PHPUnit\Framework\TestCase;

class StartSessionMiddlewareTest extends TestCase
{
    private SessionManagerInterface $session;
    private SessionStartMiddleware $middleware;

    /**
     * @throws SessionValidationException
     */
    protected function setUp(): void
    {
        $_SESSION = [];

        $this->session = new Session(new SessionConfig());

        $this->middleware = new SessionStartMiddleware($this->session);
    }

    public function testMiddleware(): void
    {
        $this->assertFalse($this->session->isStarted());

        Dispatcher::run([
            $this->middleware,
        ]);

        $this->assertFalse($this->session->isStarted());
    }
}
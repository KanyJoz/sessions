<?php

declare(strict_types=1);

namespace KanyJoz\Session\Test;

use KanyJoz\Sessions\Exception\SessionException;
use KanyJoz\Sessions\Exception\SessionValidationException;
use KanyJoz\Sessions\Interface\FlashInterface;
use KanyJoz\Sessions\Interface\SessionInterface;
use KanyJoz\Sessions\Interface\SessionManagerInterface;
use KanyJoz\Sessions\Session;
use KanyJoz\Sessions\SessionConfig;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    private SessionInterface $session;
    private SessionManagerInterface $manager;
    private FlashInterface $flasher;
    private string $flashMessageKey = '_flash_messages';

    /**
     * @throws SessionValidationException
     */
    protected function setUp(): void
    {
        $_SESSION = [];

        $this->session = new Session(new SessionConfig(), $this->flashMessageKey);
        $this->manager = $this->session;
        $this->flasher = $this->session;
    }

    protected function tearDown(): void
    {
        if (isset($this->manager) && $this->manager->isStarted()) {
            $this->manager->destroy();
        }

        unset($this->session);
        unset($this->manager);
        unset($this->flasher);
    }

    public function testStart(): void
    {
        $this->manager->start();
        $this->assertTrue($this->manager->isStarted());
        $this->assertNotEmpty($this->manager->getId());

        $this->assertArrayHasKey($this->flashMessageKey, $_SESSION);
        $this->assertSame([], $_SESSION[$this->flashMessageKey]);
    }

    public function testIsStarted(): void
    {
        $this->assertFalse($this->manager->isStarted());
        $this->manager->start();
        $this->assertTrue($this->manager->isStarted());
    }

    public function testRegenerateId(): void
    {
        $this->manager->start();
        $this->assertNotEmpty($this->manager->getId());

        $oldId = $this->manager->getId();
        $this->manager->regenerateId();
        $newId = $this->manager->getId();
        $this->assertNotSame($oldId, $newId);
    }

    public function testRegenerateIdException(): void
    {
        $this->expectException(SessionException::class);
        $this->expectExceptionMessage('Failed to regenerate session id: Session is not started yet.');

        $this->manager->regenerateId();
    }

    public function testDestroy(): void
    {
        $this->manager->start();
        $this->assertArrayHasKey($this->flashMessageKey, $_SESSION);

        $this->manager->destroy();
        $this->assertSame([], $_SESSION);
    }

    public function testGetId(): void
    {
        $this->assertEmpty($this->manager->getId());
        $this->manager->start();
        $this->assertNotEmpty($this->manager->getId());
        $this->manager->destroy();
        $this->assertEmpty($this->manager->getId());
    }

    public function testGetName(): void
    {
        $this->manager->start();
        $this->assertSame('PHPSESSID', $this->manager->getName());
    }

    public function testSave(): void
    {
        $this->assertFalse($this->manager->save());

        $this->manager->start();
        $this->assertTrue($this->manager->save());
    }

    public function testPutAndGet(): void
    {
        $this->manager->start();

        // string
        $this->session->put('key', 'value');
        $this->assertSame('value', $this->session->get('key'));

        // int
        $valueInt = 1;
        $this->session->put('key', $valueInt);
        $valueInt = $this->session->get('key');
        $this->assertSame($valueInt, $valueInt);

        // float
        $this->session->put('key', 3.14);
        $this->assertSame(3.14, $this->session->get('key'));

        // bool
        $this->session->put('key', true);
        $this->assertTrue($this->session->get('key'));

        $this->session->put('key', false);
        $this->assertFalse($this->session->get('key'));

        // default null
        $this->assertNull($this->session->get('non_existent_key'));

        // default value
        $defaultValue = 4;
        $this->assertSame($defaultValue, $this->session->get('non_existent_key', $defaultValue));
    }

    public function testHas(): void
    {
        $this->manager->start();

        $this->assertFalse($this->session->has('key'));

        $this->session->put('key', 'value');
        $this->assertTrue($this->session->has('key'));
    }

    public function testDeleteAndClear(): void
    {
        $this->manager->start();

        $this->assertNull($this->session->get('key'));

        $this->session->put('key', 'value');
        $this->assertSame('value', $this->session->get('key'));
        $this->session->put('key2', 'value2');
        $this->assertSame('value2', $this->session->get('key2'));
        $this->session->put('key3', 'value3');
        $this->assertSame('value3', $this->session->get('key3'));

        $this->session->delete('key');
        $this->assertNull($this->session->get('key'));

        $this->session->clear();
        $this->assertEmpty($_SESSION);
    }

    public function testFlashPutAndFlashGet(): void
    {
        $this->manager->start();

        $this->flasher->flashPut('key1', 'value1');
        $this->flasher->flashPut('key2', 'value2');
        $this->flasher->flashPut('key2', 'value3');

        $this->assertSame('value1', $this->flasher->flashGet('key1'));
        $this->assertSame(null, $this->flasher->flashGet('key1'));

        $this->assertSame('value3', $this->flasher->flashGet('key2'));
        $this->assertSame(null, $this->flasher->flashGet('key2'));

        $this->assertSame(null, $this->flasher->flashGet('non_existent_key'));
    }

    public function testFlashHas(): void
    {
        $this->manager->start();

        $this->flasher->flashPut('key1', 'value1');

        $this->assertTrue($this->flasher->flashHas('key1'));
        $this->assertTrue($this->flasher->flashHas('key1'));
        $this->assertFalse($this->flasher->flashHas('key2'));
    }

    public function testFlashClear(): void
    {
        $this->manager->start();

        $this->flasher->flashPut('key1', 'value1');
        $this->flasher->flashPut('key2', 'value2');
        $this->assertTrue($this->flasher->flashHas('key1'));
        $this->assertTrue($this->flasher->flashHas('key2'));


        $this->flasher->flashClear();
        $this->assertFalse($this->flasher->flashHas('key1'));
        $this->assertFalse($this->flasher->flashHas('key2'));
        $this->assertSame([], $_SESSION[$this->flashMessageKey]);
    }
}
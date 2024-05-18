<?php

declare(strict_types=1);

namespace KanyJoz\Session\Test;

use KanyJoz\Sessions\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private Validator $v;

    protected function setUp(): void
    {
        $this->v = new Validator();
    }

    public function testIsValid(): void
    {
        $this->assertTrue($this->v->isValid());

        $this->v->addError('password', 'This field should be at least 8 characters long');
        $this->assertFalse($this->v->isValid());
    }

    public function testAddError(): void
    {
        $this->assertSame([], $this->v->errors());

        $this->v->addError('password', 'This field should be at least 8 characters long');
        $this->assertSame(
            ['password' => 'This field should be at least 8 characters long'],
            $this->v->errors()
        );

        $this->v->addError('password', 'This field should not be blank');
        $this->assertSame(
            ['password' => 'This field should be at least 8 characters long'],
            $this->v->errors()
        );

        $this->v->addError('name', 'This field should not be blank');
        $this->assertSame(
            [
                'password' => 'This field should be at least 8 characters long',
                'name' => 'This field should not be blank',
            ],
            $this->v->errors()
        );
    }

    public function testCheck(): void
    {
        $this->v->check(true, 'name', 'This field should not be blank');
        $this->assertSame([], $this->v->errors());

        $this->v->check(false, 'name', 'This field should not be blank');
        $this->assertSame(['name' => 'This field should not be blank',], $this->v->errors());
    }

    public function testErrors(): void
    {
        $this->assertSame([], $this->v->errors());

        $this->v->addError('password', 'This field should be at least 8 characters long');
        $this->assertSame(
            ['password' => 'This field should be at least 8 characters long'],
            $this->v->errors()
        );
    }

    public function testPermitted(): void
    {
        $this->assertTrue($this->v->permitted(200, [200, 300, 500]));
        $this->assertFalse($this->v->permitted('Europe', []));


        $this->assertFalse($this->v->permitted('200', [200, 300, 500]));
        $this->assertFalse($this->v->permitted('', [null, 0, false]));
    }

    public function testMatches(): void
    {
        $this->assertTrue($this->v->matches('hello_world', Validator::ALPHA));
        $this->assertFalse($this->v->matches('$hello_world', Validator::ALPHA));
    }

    public function testBetween(): void
    {
        $this->assertTrue($this->v->between(10, 5, 20));
        $this->assertTrue($this->v->between(10, 10, 20));
        $this->assertTrue($this->v->between(10, 5, 10));

        $this->assertTrue($this->v->between(-1, -10, 0));

        $this->assertFalse($this->v->between(3, 5, 10));
    }
}
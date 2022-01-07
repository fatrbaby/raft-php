<?php

declare(strict_types=1);

namespace Testing;

use Raft\Lock;
use PHPUnit\Framework\TestCase;

class LockTest extends TestCase
{
    public function testNew()
    {
        $lock = new Lock(__METHOD__);

        $this->assertTrue($lock->isRegistered());
    }

    public function testAcquire()
    {
        $lock = new Lock(__METHOD__);

        $this->assertTrue($lock->acquire());
        $this->assertFalse($lock->acquire());
    }

    public function testRelease()
    {
        $lock = new Lock(__METHOD__);
        $this->assertTrue($lock->acquire());
        $this->assertFalse($lock->acquire());

        $lock->release();

        $this->assertTrue($lock->acquire());
    }

    public function testForceRelease()
    {
        // todo
    }
}

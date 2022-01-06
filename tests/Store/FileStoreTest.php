<?php

declare(strict_types=1);

namespace Testing\Storage;

use PHPUnit\Framework\TestCase;
use Raft\Store\FileStore;
use Raft\Store\StoreInterface;

class FileStoreTest extends TestCase
{
    public function testNew()
    {
        $store = new FileStore(__DIR__ . '/../var');

        $this->assertInstanceOf(StoreInterface::class, $store);
    }

    public function testPut()
    {
        $storage = new FileStore(__DIR__ . '/../var');
        $this->assertTrue($storage->put("hello"));
    }
}

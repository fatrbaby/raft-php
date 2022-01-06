<?php

declare(strict_types=1);

namespace Raft\Store;

use InvalidArgumentException;
use Raft\Lock;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class FileStore implements StoreInterface
{
    private string $database;
    private Filesystem $fs;
    private Lock $lock;

    public function __construct(string $database)
    {
        $this->database = $database;
        $this->fs = new Filesystem();
        $this->check();

        $this->lock = new Lock('kv');
    }

    public function put(string $key, string $content): bool
    {
        try {
            if ($this->lock->acquire()) {
                $this->fs->dumpFile($this->database, "{$key}:{$content}");
            }
        } finally {
            $this->lock->release();
        }
    }

    private function check()
    {
        if (!$this->fs->exists($this->database)) {
            $this->fs->touch($this->database);
        }

        if (!is_readable($this->database)) {
            throw new IOException('database is not readable');
        }
    }

    public function get(string $key): string
    {
        // TODO: Implement get() method.
    }

    private function wrapFilename(string $name): string
    {
        return rtrim($this->workdir, '/') . '/' . $name;
    }
}
<?php

declare(strict_types=1);

namespace Raft;

use RuntimeException;
use SysvSemaphore;

class Lock
{
    private const PERMISSION = 0666;

    private string $key;

    private SysvSemaphore $id;

    private int $maxAcquire;

    private bool $autoRelease;

    private bool $registered = false;

    public function __construct(string $key, int $maxAcquire = 1, bool $autoRelease = false)
    {
        $this->key = $key;
        $this->maxAcquire = $maxAcquire;
        $this->autoRelease = $autoRelease;

        $this->registerLockUnlessRegistered();
    }

    private function registerLockUnlessRegistered(): void
    {
        if (!$this->registered) {
            $this->id = sem_get(crc32($this->key), $this->maxAcquire, self::PERMISSION, $this->autoRelease);

            if ($this->id === false) {
                throw new RuntimeException('Register lock fail');
            }

            $this->registered = true;
        }
    }

    public function acquire(): bool
    {
        $this->registerLockUnlessRegistered();

        return sem_acquire($this->id, true);
    }

    public function release()
    {
        $this->registerLockUnlessRegistered();

        return sem_release($this->id);
    }

    public function forceRelease(): bool
    {
        if (!$this->registered) {
            return false;
        }

        return sem_remove($this->id);
    }

    public function getId(): SysvSemaphore
    {
        return $this->id;
    }

    public function getMaxAcquire(): int
    {
        return $this->maxAcquire;
    }

    public function isAutoRelease(): bool
    {
        return $this->autoRelease;
    }

    public function isRegistered(): bool
    {
        return $this->registered;
    }
}

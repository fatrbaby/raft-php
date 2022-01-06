<?php

declare(strict_types=1);

namespace Raft;

use RuntimeException;
use SysvSemaphore;

class Lock
{
    private const PERMISSION = 0666;
    private SysvSemaphore $id;
    private string $key;
    private int $maxAcquire;
    private bool $autoRelease;

    /**
     * @param string $key
     * @param int $maxAcquire
     * @param bool $autoRelease
     */
    public function __construct(string $key, int $maxAcquire = 1, bool $autoRelease = false)
    {
        $this->key = $key;
        $this->maxAcquire = $maxAcquire;
        $this->autoRelease = $autoRelease;

        $this->registerLockUnlessRegistered();
    }

    private function registerLockUnlessRegistered(): void
    {
        if (!$this->registered()) {
            $this->id = sem_get(
                ftok(__FILE__),
                $this->maxAcquire,
                self::PERMISSION,
                $this->autoRelease
            );

            if ($this->id === false) {
                throw new RuntimeException('Register lock fail');
            }
        }
    }

    private function registered(): bool
    {
        return $this->id instanceof SysvSemaphore;
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
        if ($this->registered()) {
            return false;
        }

        return sem_remove($this->id);
    }
}

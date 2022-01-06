<?php

declare(strict_types=1);

namespace Raft\Store;

interface StoreInterface
{
    public function put(string $key, string $content): bool;

    public function get(string $key): string;
}
<?php

declare(strict_types=1);

namespace Raft;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class Database
{
    private string $path;

    private int $permission = 0666;

    private Filesystem $fs;

    public function __construct(string $path, int $permission = 0666)
    {
        $this->path = Path::canonicalize($path);
        $this->permission = $permission;

        $this->fs = new Filesystem();
    }
}

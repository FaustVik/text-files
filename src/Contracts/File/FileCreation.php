<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\File;

interface FileCreation
{
    public function create(string $path): bool;
}

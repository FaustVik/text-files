<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\File;

interface FileContract
{
    /**
     * Get file path.
     */
    public function getPath(): string;

    /**
     * File checks: does the file exist, can it be read, written, etc.
     */
    public function checkFile(): void;

    /**
     * Get file size.
     */
    public function getSize(): int;

    /**
     * Get extension file.
     */
    public function getExtension(): string;
}

<?php

declare(strict_types=1);

namespace FaustVik\Files\File;

use FaustVik\Files\Contracts\File\FileContract;
use FaustVik\Files\Exceptions\File\FileIsNotReadableException;
use FaustVik\Files\Exceptions\File\FileIsNotWriteableException;
use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Helpers\File\FileInfo;

final class File implements FileContract
{
    public function __construct(
        private readonly string $path,
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @throws FileIsNotWriteableException
     * @throws FileNotFoundException
     * @throws FileBaseException
     * @throws FileIsNotReadableException
     */
    public function checkFile(): void
    {
        if (!FileInfo::checkFileExistence(path: $this->path)) {
            throw new FileNotFoundException($this->path);
        }

        if (!FileInfo::isFile(path: $this->path)) {
            throw new FileBaseException('Is not file');
        }

        if (!FileInfo::isReadable(path: $this->path)) {
            throw new FileIsNotReadableException($this->path);
        }

        if (!FileInfo::isWritable(path: $this->path)) {
            throw new FileIsNotWriteableException($this->path);
        }
    }

    public function getSize(): int
    {
        $size = FileInfo::getSize(path: $this->path);

        if (!$size) {
            return 0;
        }

        return $size;
    }

    public function getExtension(): string
    {
        $ext = FileInfo::getExtension(path: $this->path);

        if (!$ext) {
            return '';
        }

        return $ext;
    }
}

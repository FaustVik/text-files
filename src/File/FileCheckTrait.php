<?php

declare(strict_types=1);

namespace FaustVik\Files\File;

use FaustVik\Files\Exceptions\File\FileIsNotReadableException;
use FaustVik\Files\Exceptions\File\FileIsNotWriteableException;
use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Helpers\File\FileInfo;

/**
 * @method string getPath()
 */
trait FileCheckTrait
{
    /**
     * @throws FileIsNotWriteableException
     * @throws FileNotFoundException
     * @throws FileBaseException
     * @throws FileIsNotReadableException
     */
    protected function validateFile(): void
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
}

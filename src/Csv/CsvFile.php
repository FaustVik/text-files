<?php

declare(strict_types=1);

namespace FaustVik\Files\Csv;

use FaustVik\Files\Contracts\File\CsvFileContract;
use FaustVik\Files\Exceptions\File\FileExtensionIsNotSupportedException;
use FaustVik\Files\Exceptions\File\FileIsNotReadableException;
use FaustVik\Files\Exceptions\File\FileIsNotWriteableException;
use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Helpers\File\FileInfo;

final class CsvFile implements CsvFileContract
{
    public const TYPE_CSV = 'csv';

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
     * @throws FileExtensionIsNotSupportedException
     * @throws FileBaseException
     * @throws FileIsNotReadableException
     */
    public function checkFile(): void
    {
        if (!FileInfo::checkFileExistence($this->path)) {
            throw new FileNotFoundException($this->path);
        }

        if (!FileInfo::isFile($this->path)) {
            throw new FileBaseException('Is not file');
        }

        if (!FileInfo::isReadable($this->path)) {
            throw new FileIsNotReadableException($this->path);
        }

        if (!FileInfo::isWritable($this->path)) {
            throw new FileIsNotWriteableException($this->path);
        }

        if (!$this->isCsvExtension()) {
            throw new FileExtensionIsNotSupportedException($this->getExtension());
        }
    }

    /**
     * @throws FileNotFoundException
     */
    public function getSize(): int
    {
        $size = FileInfo::getSize($this->path);

        if (!$size) {
            return 0;
        }

        return $size;
    }

    /**
     * @throws FileNotFoundException
     */
    public function getExtension(): string
    {
        $ext = FileInfo::getExtension($this->path);

        if (!$ext) {
            return '';
        }

        return $ext;
    }

    /**
     * @throws FileNotFoundException
     */
    public function isCsvExtension(): bool
    {
        return $this->getExtension() === self::TYPE_CSV;
    }
}

<?php

declare(strict_types=1);

namespace FaustVik\Files\Helpers\File;

use FaustVik\Files\Dictionary\FileModes;
use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Exceptions\IsNotResourceException;

/**
 * Class FileOperation.
 */
final class FileOperation
{
    /**
     * Delete file.
     *
     * @throws FileBaseException
     */
    public static function delete(string $path): bool
    {
        if (!FileInfo::checkFileExistence(path: $path)) {
            throw new FileNotFoundException($path);
        }

        if (!unlink(filename: $path)) {
            throw new FileBaseException(\sprintf('Failed to delete file "%s"', $path));
        }

        return true;
    }

    /**
     * Internal method for moving or copying files.
     *
     * @throws FileBaseException
     * @throws FileNotFoundException
     */
    private static function moving(string $from, string $to, bool $deleteOriginal = false): bool
    {
        self::validatePath($from);
        self::validatePath($to);

        if (!FileInfo::checkFileExistence(path: $from)) {
            throw new FileNotFoundException($from);
        }

        if (FileInfo::checkFileExistence(path: $to)) {
            throw new FileBaseException(\sprintf('File "%s" already exists', $to));
        }

        if (!copy(from: $from, to: $to)) {
            throw new FileBaseException(\sprintf('Failed to copy file from "%s" to "%s"', $from, $to));
        }

        if ($deleteOriginal && !self::delete(path: $from)) {
            throw new FileBaseException(\sprintf('Failed to delete original file "%s"', $from));
        }

        return true;
    }

    /**
     * Copy file.
     *
     * @throws FileBaseException
     * @throws FileNotFoundException
     */
    public static function copy(string $from, string $to): bool
    {
        return self::moving(from: $from, to: $to);
    }

    /**
     * Move file.
     *
     * @throws FileBaseException
     * @throws FileNotFoundException
     */
    public static function move(string $from, string $to): bool
    {
        return self::moving(from: $from, to: $to, deleteOriginal: true);
    }

    /**
     * Rename file.
     *
     * @throws FileBaseException
     * @throws FileNotFoundException
     */
    public static function rename(string $path, string $newName): string
    {
        self::validatePath($path);

        if (!FileInfo::checkFileExistence(path: $path)) {
            throw new FileNotFoundException($path);
        }

        $nameOld = FileInfo::getName(path: $path);
        $newPath = str_replace(search: $nameOld, replace: $newName, subject: $path);

        if (!rename(from: $path, to: $newPath)) {
            throw new FileBaseException(\sprintf('Failed to rename file "%s" to "%s"', $path, $newPath));
        }

        return $newPath;
    }

    /**
     * Open file.
     *
     * @return resource
     * @throws FileBaseException
     */
    public static function openFile(string $path, FileModes $mode)
    {
        self::validatePath($path);

        $handle = fopen(filename: $path, mode: $mode->value);

        if ($handle === false) {
            throw new FileBaseException(\sprintf('Failed to open file "%s"', $path));
        }

        return $handle;
    }

    /**
     * Close file.
     *
     * @param resource $handle
     * @throws IsNotResourceException
     */
    public static function closeFile($handle): bool
    {
        if (!\is_resource(value: $handle)) {
            throw new IsNotResourceException();
        }

        return fclose(stream: $handle);
    }

    /**
     * Clear file content.
     *
     * @param string|resource $handle
     * @throws FileBaseException
     */
    public static function flush($handle): bool
    {
        if (!\is_resource(value: $handle)) {
            if (!\is_string($handle)) {
                throw new \InvalidArgumentException('The handle must be a string or a resource.');
            }
            $handle = self::openFile(path: $handle, mode: FileModes::WRITE_READ_TRUNC);
        }

        if (!ftruncate(stream: $handle, size: 0)) {
            throw new FileBaseException('Failed to truncate file');
        }

        return self::closeFile(handle: $handle);
    }

    /**
     * Validate file path.
     *
     * @throws FileBaseException
     */
    private static function validatePath(string $path): void
    {
        if ($path === '') {
            throw new FileBaseException('Path cannot be empty');
        }

        if (str_contains(haystack: $path, needle: "\0")) {
            throw new FileBaseException('Path contains invalid characters');
        }
    }
}

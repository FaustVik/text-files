<?php

declare(strict_types=1);

namespace FaustVik\Files\Helpers\Directory;

use FaustVik\Files\Exceptions\DirectoryExceptionBase;
use FaustVik\Files\Exceptions\IsNotResourceException;
use FaustVik\Files\Helpers\File\FileOperation;

/**
 * Class DirectoryOperation.
 */
final class DirectoryOperation
{
    /**
     * Create a directory (optionally recursive).
     *
     * @throws \RuntimeException
     */
    public static function createDir(string $path, bool $recursive = false): void
    {
        if (!is_dir(filename: $path) && !mkdir(directory: $path, permissions: 0o777, recursive: $recursive) && !is_dir(filename: $path)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $path));
        }
    }

    /**
     * Delete a directory recursively.
     *
     * @throws DirectoryExceptionBase
     * @throws IsNotResourceException
     */
    public static function deleteDir(string $path): void
    {
        if (!is_dir(filename: $path)) {
            throw new DirectoryExceptionBase(\sprintf('Directory "%s" does not exist', $path));
        }

        $dir = self::openDir(path: $path);

        while (false !== ($file = readdir(dir_handle: $dir))) {
            if ($file !== '.' && $file !== '..') {
                $pathResource = $path . \DIRECTORY_SEPARATOR . $file;
                if (is_dir(filename: $pathResource)) {
                    self::deleteDir(path: $pathResource);
                } else {
                    FileOperation::delete(path: $pathResource);
                }
            }
        }

        self::closeDir(handle: $dir);

        if (!rmdir(directory: $path)) {
            throw new \RuntimeException(\sprintf('Failed to delete directory "%s"', $path));
        }
    }

    /**
     * Open a directory.
     *
     * @return resource
     * @throws DirectoryExceptionBase
     */
    public static function openDir(string $path)
    {
        if (!is_dir(filename: $path)) {
            throw new DirectoryExceptionBase(\sprintf('Directory "%s" does not exist', $path));
        }

        $handle = opendir(directory: $path);
        if ($handle === false) {
            throw new DirectoryExceptionBase(\sprintf('Cannot open directory "%s"', $path));
        }

        return $handle;
    }

    /**
     * Close a directory.
     *
     * @param resource $handle
     * @throws IsNotResourceException
     */
    public static function closeDir($handle): void
    {
        if (!\is_resource(value: $handle)) {
            throw new IsNotResourceException();
        }

        closedir(dir_handle: $handle);
    }
}

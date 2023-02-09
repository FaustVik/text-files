<?php

declare(strict_types=1);

namespace FaustVik\Files\Helpers\File;

use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Files\Exceptions\FileBaseException;

/**
 * Class FileInfo.
 */
final class FileInfo
{
    /**
     * Clear the stat cache for a specific file.
     */
    public static function clearStatCache(string $filename, bool $clearRealpathCache = true): void
    {
        clearstatcache(clear_realpath_cache: $clearRealpathCache, filename: $filename);
    }

    /**
     * Check if a file exists.
     */
    public static function checkFileExistence(string $path, bool $clearCache = true): bool
    {
        if ($clearCache) {
            self::clearStatCache(filename: $path);
        }

        return file_exists(filename: $path);
    }

    /**
     * Validate file existence and throw an exception if it doesn't exist.
     *
     * @throws FileNotFoundException
     */
    private static function validateFileExistence(string $path, bool $clearCache = true): void
    {
        if (!self::checkFileExistence(path: $path, clearCache: $clearCache)) {
            throw new FileNotFoundException(\sprintf('File "%s" does not exist', $path));
        }
    }

    /**
     * Get the base name of the file.
     *
     * @throws FileNotFoundException
     */
    public static function getName(string $path, string $suffix = ''): string
    {
        self::validateFileExistence(path: $path);

        return basename(path: $path, suffix: $suffix);
    }

    /**
     * Get the size of the file in bytes.
     *
     * @throws FileNotFoundException
     *
     * @return int<0, max>
     */
    public static function getSize(string $path): int
    {
        self::validateFileExistence(path: $path);

        $size = filesize(filename: $path);
        return $size === false ? 0 : $size;
    }

    /**
     * Get the extension of the file.
     *
     * @throws FileNotFoundException
     */
    public static function getExtension(string $path): string
    {
        self::validateFileExistence(path: $path);

        return pathinfo(path: $path, flags: PATHINFO_EXTENSION);
    }

    /**
     * Get full information about the file.
     *
     * @throws FileNotFoundException
     *
     * @return array{dirname?: string, basename: string, extension?: string, filename: string}
     */
    public static function getFullInfo(string $filename): array
    {
        self::validateFileExistence(path: $filename);

        return pathinfo(path: $filename);
    }

    /**
     * Check if the file is writable.
     *
     * @throws FileNotFoundException
     */
    public static function isWritable(string $path): bool
    {
        self::validateFileExistence(path: $path);

        return is_writable(filename: $path);
    }

    /**
     * Check if the file is readable.
     *
     * @throws FileNotFoundException
     */
    public static function isReadable(string $path): bool
    {
        self::validateFileExistence(path: $path);

        return is_readable(filename: $path);
    }

    /**
     * Get the owner of the file.
     *
     * @return array{name: string, passwd: string, uid: int, gid: int, gecos: string, dir: string, shell: string}
     *@throws \RuntimeException if the owner information cannot be retrieved
     * @throws FileBaseException
     *
     * @throws FileNotFoundException
     */
    public static function getFileOwner(string $path): array
    {
        self::validateFileExistence(path: $path);

        $userId = fileowner(filename: $path);

        if ($userId === false) {
            throw new FileBaseException('Cant get info by owner');
        }

        $ownerInfo = posix_getpwuid(user_id: $userId);

        if ($ownerInfo === false) {
            throw new \RuntimeException(\sprintf('Unable to retrieve owner information for file "%s"', $path));
        }

        return $ownerInfo;
    }

    /**
     * Get the modification time of the file.
     *
     * @throws FileNotFoundException
     */
    public static function getFileTime(string $path): int
    {
        self::validateFileExistence(path: $path);

        $time = filemtime(filename: $path);
        return $time === false ? 0 : $time;
    }

    /**
     * Check if the path is a regular file.
     *
     * @throws FileNotFoundException
     */
    public static function isFile(string $path): bool
    {
        self::validateFileExistence(path: $path);

        return is_file(filename: $path);
    }
}

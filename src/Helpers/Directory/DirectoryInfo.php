<?php

declare(strict_types=1);

namespace FaustVik\Files\Helpers\Directory;

use FaustVik\Files\Exceptions\DirectoryExceptionBase;

/**
 * Class DirectoryInfo.
 */
final class DirectoryInfo
{
    /** @var array<string, false|string> */
    private static array $realPathCache = [];

    /**
     * Directory scan.
     *
     * @return array<string>
     * @throws DirectoryExceptionBase
     */
    public static function scan(string $path, EnumSortDirectory $sort = EnumSortDirectory::NoSort): array
    {
        $realPath = self::getRealPath(path: $path);
        if ($realPath === false) {
            throw new DirectoryExceptionBase(\sprintf('Directory "%s" does not exist or is not accessible', $path));
        }

        $list = scandir(directory: $realPath, sorting_order: $sort->value);
        if ($list === false) {
            throw new DirectoryExceptionBase(\sprintf('Failed to scan directory "%s"', $path));
        }

        return $list;
    }

    /**
     * Does a directory exist and is it a directory.
     */
    public static function isDirExist(string $folder): bool
    {
        $realPath = self::getRealPath(path: $folder);

        return $realPath !== false && is_dir(filename: $realPath);
    }

    /**
     * Clear the real path cache.
     */
    public static function clearCache(): void
    {
        self::$realPathCache = [];
    }

    private static function getRealPath(string $path): false|string
    {
        if (isset(self::$realPathCache[$path])) {
            return self::$realPathCache[$path];
        }

        $realPath = realpath(path: $path);
        self::$realPathCache[$path] = $realPath;

        return $realPath;
    }
}

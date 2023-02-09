<?php

declare(strict_types=1);

namespace FaustVik\Files\Helpers\Directory;

use FaustVik\Files\Exceptions\DirectoryExceptionBase;

/**
 * Class DirectoryInfo.
 */
final class DirectoryInfo
{
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

    private static function getRealPath(string $path): false|string
    {
        static $realPathCache = [];

        if (isset($realPathCache[$path])) {
            return $realPathCache[$path];
        }

        $realPath = realpath(path: $path);
        $realPathCache[$path] = $realPath;

        return $realPath;
    }
}

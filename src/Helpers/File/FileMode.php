<?php

declare(strict_types=1);

namespace FaustVik\Files\Helpers\File;

/**
 * Enum FileMode.
 */
enum FileMode: string
{
    case ONLY_READ = 'r';
    case READ_WRITE = 'r+';
    case ONLY_READ_BINARY = 'rb';
    case READ_WRITE_BINARY = 'rb+';
    case WRITE_TRUNC_ONLY = 'w';
    case WRITE_READ_TRUNC = 'w+';
    case WRITE_TRUNC_ONLY_BINARY = 'wb';
    case WRITE_READ_TRUNC_BINARY = 'wb+';
    case WRITE_APPEND_ONLY = 'a';
    case WRITE_READ_APPEND = 'a+';
    case WRITE_APPEND_ONLY_BINARY = 'ab';
    case WRITE_READ_APPEND_BINARY = 'ab+';
    case WRITE_ONLY_NEW_FILE = 'x';
    case WRITE_READ_ONLY_NEW_FILE = 'x+';
    case WRITE_ONLY_NEW_FILE_BINARY = 'xb';
    case WRITE_READ_ONLY_NEW_BINARY = 'xb+';
    case WRITE_ONLY_START_FILE = 'c';
    case WRITE_READ_START_FILE = 'c+';
    case WRITE_ONLY_START_FILE_BINARY = 'cb';
    case WRITE_READ_START_FILE_BINARY = 'cb+';
    case CLOSE_ON_EXEC = 'e';

    /**
     * Check if a mode is valid.
     */
    public static function isValidMode(string $mode): bool
    {
        return self::tryFrom($mode) !== null;
    }
}

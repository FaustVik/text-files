<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileBaseException;

/**
 * Class FileIsNotReadable.
 */
final class FileIsNotReadableException extends FileBaseException
{
    public function __construct(string $message = '')
    {
        parent::__construct(\sprintf('File is not readable:  %s', $message));
    }
}

<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileBaseException;

/**
 * Class ReadFile.
 */
final class CantReadFileException extends FileBaseException
{
    public function __construct(string $message = '')
    {
        parent::__construct(\sprintf("Can't read file: %s", $message));
    }
}

<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileBaseException;

/**
 * Class FileNotFound.
 */
final class FileNotFoundException extends FileBaseException
{
    public function __construct(string $path)
    {
        parent::__construct(\sprintf('File "%s" not found', $path));
    }
}

<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileException;

/**
 * Class FileNotFound
 * @package FaustVik\Files\Exceptions\File
 */
class FileNotFound extends FileException
{
    public function __construct(string $path)
    {
        parent::__construct(sprintf("File not found. Path:  %s", $path));
    }
}

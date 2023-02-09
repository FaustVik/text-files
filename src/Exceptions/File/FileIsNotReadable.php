<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileException;

/**
 * Class FileIsNotReadable
 * @package FaustVik\Files\Exceptions\File
 */
class FileIsNotReadable extends FileException
{
    public function __construct(string $message = "")
    {
        parent::__construct(sprintf("File is not readable:  %s", $message));
    }
}

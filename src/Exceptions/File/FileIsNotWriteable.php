<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileException;

/**
 * Class FileIsNotWriteable
 * @package FaustVik\Files\Exceptions\File
 */
class FileIsNotWriteable extends FileException
{
    public function __construct(string $message = "")
    {
        parent::__construct(sprintf("File is not writeable:  %s", $message));
    }
}

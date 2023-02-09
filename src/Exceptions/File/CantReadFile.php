<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileException;

/**
 * Class ReadFile
 * @package FaustVik\Files\Exceptions\File
 */
class CantReadFile extends FileException
{
    public function __construct(string $message = '')
    {
        parent::__construct(sprintf("Can't read file: %s", $message));
    }
}

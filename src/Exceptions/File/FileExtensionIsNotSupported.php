<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileException;

/**
 * Class FileExtensionIsNotSupported
 * @package FaustVik\Files\Exceptions\File
 */
class FileExtensionIsNotSupported extends FileException
{
    public function __construct(string $extension)
    {
        parent::__construct(sprintf("Extension %s is not supported", $extension));
    }
}

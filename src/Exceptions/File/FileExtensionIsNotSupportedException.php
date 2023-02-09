<?php

declare(strict_types=1);

namespace FaustVik\Files\Exceptions\File;

use FaustVik\Files\Exceptions\FileBaseException;

/**
 * Class FileExtensionIsNotSupported.
 */
final class FileExtensionIsNotSupportedException extends FileBaseException
{
    public function __construct(string $extension)
    {
        parent::__construct(\sprintf('Extension "%s" is not supported', $extension));
    }
}

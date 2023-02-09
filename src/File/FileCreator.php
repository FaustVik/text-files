<?php

declare(strict_types=1);

namespace FaustVik\Files\File;

use FaustVik\Files\Contracts\File\FileCreation;
use FaustVik\Files\Contracts\File\FileOperationsInterface;
use FaustVik\Files\Dictionary\FileModes;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Exceptions\IsNotResourceException;
use FaustVik\Files\Helpers\File\FileInfo;

final class FileCreator implements FileCreation
{
    public function __construct(
        private readonly FileOperationsInterface $fileOperations,
    ) {
    }

    /**
     * Create a file if it does not exist.
     *
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function create(string $path): bool
    {
        if (FileInfo::checkFileExistence(path: $path)) {
            return true;
        }

        $handle = $this->fileOperations->openFile(path: $path, mode: FileModes::WRITE_TRUNC_ONLY);

        if (!$this->fileOperations->closeFile(handle: $handle)) {
            throw new FileBaseException('Failed to close the file after creation');
        }

        return true;
    }
}

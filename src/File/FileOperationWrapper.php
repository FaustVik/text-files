<?php

declare(strict_types=1);

namespace FaustVik\Files\File;

use FaustVik\Files\Contracts\File\FileOperationsInterface;
use FaustVik\Files\Dictionary\FileModes;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Exceptions\IsNotResourceException;
use FaustVik\Files\Helpers\File\FileOperation;

/**
 * Wrapper for FileOperation that implements FileOperationsInterface.
 * Delegates calls to the static methods of FileOperation.
 */
final class FileOperationWrapper implements FileOperationsInterface
{
    /**
     * Opens a file at the specified path in the given mode.
     *
     * @param string $path The path to the file.
     * @param FileModes $mode The mode in which to open the file.
     * @return resource The file resource.
     * @throws FileBaseException If the file cannot be opened.
     */
    public function openFile(string $path, FileModes $mode)
    {
        return FileOperation::openFile(path: $path, mode: $mode);
    }

    /**
     * Closes the file represented by the resource.
     *
     * @param resource $handle The file resource.
     * @return bool True if the file was successfully closed, otherwise false.
     * @throws IsNotResourceException If the provided argument is not a resource.
     */
    public function closeFile($handle): bool
    {
        return FileOperation::closeFile(handle: $handle);
    }
}

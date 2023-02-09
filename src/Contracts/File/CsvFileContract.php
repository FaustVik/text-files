<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\File;

interface CsvFileContract extends FileContract
{
    /**
     * Check that the file extension is сsv.
     */
    public function isCsvExtension(): bool;
}

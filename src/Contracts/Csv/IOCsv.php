<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\Csv;

use FaustVik\Files\Exceptions\FileBaseException;

/**
 * Interface for basic CSV file operations such as reading and writing.
 */
interface IOCsv
{
    /**
     * Writes data to the CSV file.
     *
     * @param array<array<int|string, bool|float|int|string|null>> $fields The data to write.
     * @return bool True on success, false on failure.
     * @throws FileBaseException If an error occurs during writing.
     */
    public function write(array $fields): bool;

    /**
     * Reads data from the CSV file.
     *
     * @param int<0, max> $length The maximum number of rows to read. If 0, reads all rows.
     * @param int|null $line The specific line to read (optional).
     * @return array<array<int|string, int|string|float>> The read data.
     * @throws FileBaseException If an error occurs during reading.
     */
    public function read(int $length = 0, ?int $line = null): array;

    /**
     * Overwrites the entire CSV file with new data.
     *
     * @param array<array<int|string, bool|float|int|string|null>> $fields The new data.
     * @return bool True on success, false on failure.
     * @throws FileBaseException If an error occurs during overwriting.
     */
    public function overWrite(array $fields): bool;
}

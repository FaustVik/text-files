<?php

declare(strict_types=1);

namespace FaustVik\Files\Csv;

use FaustVik\Files\Contracts\Csv\CsvContract;
use FaustVik\Files\Contracts\Csv\CsvSettingReaderContract;
use FaustVik\Files\Contracts\File\CsvFileContract;
use FaustVik\Files\Contracts\File\FileOperationsInterface;
use FaustVik\Files\Dictionary\FileModes;
use FaustVik\Files\Exceptions\File\FileExtensionIsNotSupportedException;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Exceptions\IsNotResourceException;

final class CsvManager implements CsvContract
{
    /**
     * @throws FileExtensionIsNotSupportedException
     */
    public function __construct(
        private readonly CsvFileContract $file,
        private readonly CsvSettingReaderContract $csvSettingReaderContract,
        private readonly FileOperationsInterface $fileOperation,
    ) {
        $this->file->checkFile();

        if (!$this->file->isCsvExtension()) {
            throw new FileExtensionIsNotSupportedException(extension: $this->file->getExtension());
        }
    }

    /**
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function deleteColumn(array $columns): bool
    {
        if (empty($columns)) {
            return false;
        }
        $data = $this->readData();

        return $this->modifyData(data: $data, modifier: static function ($item) use ($columns) {
            foreach ($columns as $column) {
                unset($item[$column]);
            }

            return $item;
        });
    }

    /**
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function deleteLine(array $lines): bool
    {
        if ($lines === []) {
            return false;
        }

        $data = $this->read();

        if ($data === []) {
            return false;
        }

        foreach ($lines as $line) {
            if (isset($data[$line])) {
                unset($data[$line]);
            }
        }

        return $this->overWrite($data);
    }

    /**
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function updateHeaders(array $headers): bool
    {
        if (empty($headers)) {
            return false;
        }
        $data = $this->readData();

        return $this->modifyData(data: $data, modifier: static function ($item) use ($headers, $data) {
            if ($item === $data[0]) {
                foreach ($headers as $position => $name) {
                    if (isset($item[$position])) {
                        $item[$position] = $name;
                    }
                }
            }

            return $item;
        });
    }

    /**
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function getHeadersColumn(): array
    {
        $result = $this->read(0, 1);

        return $result[0];
    }

    /**
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function getColumns(array $columns): array
    {
        if (empty($columns)) {
            return [];
        }
        $handle = $this->fileOperation->openFile(path: $this->file->getPath(), mode: FileModes::ONLY_READ_BINARY);
        $data = $this->readBase(handle: $handle, length: 0, columns: $columns);
        $this->fileOperation->closeFile($handle);

        return $data;
    }

    /**
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function getLines(array $lines): array
    {
        if (empty($lines)) {
            return [];
        }
        $handle = $this->fileOperation->openFile(path: $this->file->getPath(), mode: FileModes::ONLY_READ_BINARY);
        $data = $this->readBase(handle: $handle, length: 0, lines: $lines);
        $this->fileOperation->closeFile($handle);

        return array_filter($data, static fn($key) => \in_array($key, $lines, true), ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array<array<int|string, bool|float|int|string|null>> $fields
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function write(array $fields): bool
    {
        $handle = $this->fileOperation->openFile(path: $this->file->getPath(), mode: FileModes::WRITE_APPEND_ONLY);
        $result = $this->baseWrite(handle: $handle, fields: $fields);
        $this->fileOperation->closeFile($handle);

        return $result;
    }

    /**
     * @param int<0, max> $length
     * @return array<array<int|string, int|string|float>>
     *
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function read(int $length = 0, ?int $line = null): array
    {
        $handle = $this->fileOperation->openFile(path: $this->file->getPath(), mode: FileModes::ONLY_READ_BINARY);
        $result = $this->readBase(handle: $handle, length: $length);
        $this->fileOperation->closeFile($handle);

        return $result;
    }

    /**
     * @param array<array<int|string, bool|float|int|string|null>> $fields
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function overWrite(array $fields): bool
    {
        $handle = $this->fileOperation->openFile(path: $this->file->getPath(), mode: FileModes::WRITE_TRUNC_ONLY);
        $result = $this->baseWrite(handle: $handle, fields: $fields);
        $this->fileOperation->closeFile($handle);

        return $result;
    }

    /**
     * @param resource $handle
     * @param array<array<int|string, bool|float|int|string|null>> $fields
     */
    private function baseWrite($handle, array $fields): bool
    {
        if (\count($fields) === \count($fields, COUNT_RECURSIVE)) {
            $fields = [$fields];
        }

        $result = true;
        foreach ($fields as $field) {
            $line = $this->csvSettingReaderContract->formatCsvLine(fields: $field);
            if (fwrite(stream: $handle, data: $line . "\n") === false) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * @param resource $handle
     * @param int<0, max> $length
     * @param array<int, int> $lines
     * @param array<int, int> $columns
     * @return array<array<int|string,string|int>>
     */
    private function readBase($handle, int $length, array $lines = [], array $columns = []): array
    {
        $isWasSkippedHeader = false;
        $counter = 0;
        $result = [];
        $alreadySelectedLine = [];

        while (
            ($data = fgetcsv(
                stream: $handle,
                length: $length,
                separator: $this->csvSettingReaderContract->getSeparator(),
                enclosure: $this->csvSettingReaderContract->getEnclosureChar(),
                escape: $this->csvSettingReaderContract->getEscapeChar(),
            )) !== false
        ) {
            if (!$isWasSkippedHeader && $this->csvSettingReaderContract->isSkipFirstLine()) {
                $isWasSkippedHeader = true;

                continue;
            }
            // Check lines
            if (!empty($lines)) {
                if (\count($alreadySelectedLine) === \count($lines)) {
                    break;
                }
                if (!\in_array($counter, $alreadySelectedLine, true) && \in_array($counter, $lines, true)) {
                    $result[$counter] = $this->replaceAssociations($data);
                    $alreadySelectedLine[] = $counter;
                    ++$counter;

                    continue;
                }
            }
            if (!empty($columns)) {
                $result[] = $this->replaceAssociations(
                    array_map(static fn($x) => $data[$x], $columns),
                );
                ++$counter;

                continue;
            }
            $result[] = $data;
            ++$counter;
        }

        return $result;
    }

    /**
     * Replace indexed key on custom key.
     *
     * @param array<int,string> $row
     *
     * @return array<int|string,int|string>
     */
    private function replaceAssociations(array $row): array
    {
        if (empty($this->csvSettingReaderContract->getAssociationsIndexKeys())) {
            return $row;
        }
        foreach ($row as $k => $line) {
            if (isset($this->csvSettingReaderContract->getAssociationsIndexKeys()[$k])) {
                unset($row[$k]);
                $row[$this->csvSettingReaderContract->getAssociationsIndexKeys()[$k]] = $line;
            }
        }

        return $row;
    }

    /**
     * @param int<0, max> $length
     * @return int[][]|string[][]
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    private function readData(int $length = 0): array
    {
        $handle = $this->fileOperation->openFile(path: $this->file->getPath(), mode: FileModes::ONLY_READ_BINARY);
        $data = $this->readBase(handle: $handle, length: $length);
        $this->fileOperation->closeFile($handle);

        return $data;
    }

    /**
     * @param int[][]|string[][] $data
     * @param callable $modifier
     * @return bool
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    private function modifyData(array &$data, callable $modifier): bool
    {
        if (empty($data)) {
            return false;
        }
        foreach ($data as $k => $item) {
            $data[$k] = $modifier($item);
        }

        return $this->overWrite($data);
    }
}

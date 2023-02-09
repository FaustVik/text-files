<?php

declare(strict_types=1);

namespace FaustVik\Files\Csv;

use FaustVik\Files\Contracts\Csv\CsvSettingReaderContract;

/**
 * This class provides settings for reading and writing CSV files.
 */
final class CsvSettingReader implements CsvSettingReaderContract
{
    /** @var array<int|string,string> */
    private array $associationsIndexKeys = [];

    /**
     * @param string $separator the separator character used in the CSV file
     * @param bool $skipFirstLine flag indicating whether to skip the first line when reading
     * @param bool $useAssociationForHeader flag indicating whether to use associations for header keys
     * @param string $encoding the encoding type used for reading/writing the CSV file
     * @param string $escapeChar the escape character used in the CSV file
     * @param string $enclosureChar the enclosure character used in the CSV file
     */
    public function __construct(
        private readonly string $separator = ',',
        private readonly bool $skipFirstLine = false,
        private readonly bool $useAssociationForHeader = false,
        private readonly string $encoding = 'UTF-8',
        private readonly string $escapeChar = '\\',
        private readonly string $enclosureChar = '"',
    ) {
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function isSkipFirstLine(): bool
    {
        return $this->skipFirstLine;
    }

    public function useAssociationForHeader(): bool
    {
        return $this->useAssociationForHeader;
    }

    public function setAssociationsIndexKeys(array $list): self
    {
        $this->associationsIndexKeys = $list;

        return $this;
    }

    public function getAssociationsIndexKeys(): array
    {
        return $this->associationsIndexKeys;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getEscapeChar(): string
    {
        return $this->escapeChar;
    }

    public function getEnclosureChar(): string
    {
        return $this->enclosureChar;
    }

    public function formatCsvLine(array $fields): string
    {
        return implode($this->getSeparator(), array_map(function (mixed $value) {
            if (is_array($value)) {
                $value = implode($this->getSeparator(), $value);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $value = '';
            } elseif (is_float($value) || is_int($value)) {
                $value = (string) $value;
            }

            return str_replace(search: '"', replace: '""', subject: $value);
        }, $fields));
    }
}

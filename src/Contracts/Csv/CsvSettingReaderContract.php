<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\Csv;

/**
 * Interface for CSV file settings and configurations.
 */
interface CsvSettingReaderContract
{
    /**
     * Get the separator used in the CSV file.
     *
     * @return string The separator character (e.g., ',', ';').
     */
    public function getSeparator(): string;

    /**
     * Determine whether to skip the first line when reading the CSV file.
     *
     * @return bool True if the first line should be skipped, false otherwise.
     */
    public function isSkipFirstLine(): bool;

    /**
     * Determine whether to use associations for header keys.
     *
     * @return bool True if custom associations should be used, false otherwise.
     */
    public function useAssociationForHeader(): bool;

    /**
     * Set rules for replacing indexed keys with custom names for human readability.
     *
     * @param array<int, string> $list Associative array where keys are original indices and values are new names.
     * @return self Returns the instance for method chaining.
     * @example [1 => 'name'] will result in ['name' => 'Victor'] instead of [1 => 'Victor'].
     */
    public function setAssociationsIndexKeys(array $list): self;

    /**
     * Get the associations index keys.
     *
     * @return array<int|string, string> Associative array of original indices and their corresponding custom names.
     */
    public function getAssociationsIndexKeys(): array;

    /**
     * Get the encoding used for reading/writing the CSV file.
     *
     * @return string The encoding type (e.g., 'UTF-8', 'ISO-8859-1').
     */
    public function getEncoding(): string;

    /**
     * Get the enclosure character used in the CSV file.
     *
     * @return string The enclosure character (default: '"').
     */
    public function getEnclosureChar(): string;

    /**
     * Get the escape character used in the CSV file.
     *
     * @return string The escape character (default: '\').
     */
    public function getEscapeChar(): string;

    /**
     * @param array<array<int|string, bool|float|int|string|null>|bool|float|int|string|null> $fields
     * @return string
     */
    public function formatCsvLine(array $fields): string;
}

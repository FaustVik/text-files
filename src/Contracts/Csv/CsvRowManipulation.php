<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\Csv;

/**
 * Interface for manipulating rows and columns in a CSV file.
 * Provides methods to delete, update, and retrieve specific rows or columns.
 */
interface CsvRowManipulation
{
    /**
     * Deletes specified columns from the CSV file. Columns are indexed starting from 0.
     *
     * @param array<int, int> $columns Indices of the columns to delete.
     * @return bool True if the columns were successfully deleted, false otherwise.
     */
    public function deleteColumn(array $columns): bool;

    /**
     * Deletes specified lines from the CSV file. Lines are indexed starting from 0.
     *
     * @param array<int, int> $lines Indices of the lines to delete.
     * @return bool True if the lines were successfully deleted, false otherwise.
     */
    public function deleteLine(array $lines): bool;

    /**
     * Updates the headers (first line) of the CSV file.
     *
     * @param array<int, int|string|float> $headers New headers to set.
     * @return bool True if the headers were successfully updated, false otherwise.
     */
    public function updateHeaders(array $headers): bool;

    /**
     * Retrieves the headers (first row) of the CSV file as an array.
     *
     * @return array<int|string, int|string|float> The headers as an associative or indexed array.
     */
    public function getHeadersColumn(): array;

    /**
     * Retrieves specific columns from the CSV file. Columns are indexed starting from 0.
     *
     * @param array<int, int> $columns Indices of the columns to retrieve.
     * @return array<array<int|string, int|string|float>> The selected columns as an array of rows.
     */
    public function getColumns(array $columns): array;

    /**
     * Retrieves specific lines from the CSV file. Lines are indexed starting from 0.
     *
     * @param array<int, int> $lines Indices of the lines to retrieve.
     * @return array<array<int|string, int|string|float>> The selected lines as an array of rows.
     */
    public function getLines(array $lines): array;
}

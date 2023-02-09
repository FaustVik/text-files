<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\Text;

/**
 * Interface IoTextInterface.
 */
interface IoTextInterface
{
    /**
     * reading a file into an array. each row is a new array element.
     * @param null|int<1,max> $length The maximum length of a line to read.
     * @param null|int<1,max> $limit The maximum number of rows to read
     * @return array<int, string>
     */
    public function readToArray(?int $length = null, ?int $limit = null): array;

    /**
     * reading a file into a string, you can control its length with offset and string length.
     * @param int<0, max> $offset
     * @param null|int<1, max> $length
     */
    public function readToString(int $offset = 0, ?int $length = null): string;

    /**
     * Flush the file and write new $text to the beginning of the file.
     * If the passed text is not an array, then it will be tried to be cast to a string.
     *
     * @param string|array<int|string, int|string|bool|float> $data
     */
    public function overWrite(string|array $data): bool;

    /**
     *  Is append to the end of the file.
     * If the passed text is not an array, then it will be tried to be cast to a string.
     *
     * @param string|array<int|string, int|string|bool|float> $data
     */
    public function write(string|array $data): bool;

    /**
     * Append text to the start of the file.
     *
     * @param array<int|string, int|string|bool|float>|string $text
     */
    public function appendToStartFile(array|string $text): bool;
}

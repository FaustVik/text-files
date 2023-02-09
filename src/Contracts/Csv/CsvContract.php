<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\Csv;

/**
 * Combined interface for CSV operations and manipulations.
 */
interface CsvContract extends IOCsv, CsvRowManipulation
{
}

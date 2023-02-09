<?php

declare(strict_types=1);

namespace FaustVik\Files\Contracts\Text;

interface TextSettingReaderContract
{
    public function isSkipEmptyLine(): bool;

    /**
     * @param array<int|string, int|string|bool|float> $text
     */
    public function textToString(array $text): string;
}

<?php

declare(strict_types=1);

namespace FaustVik\Files\Text;

use FaustVik\Files\Contracts\Text\TextSettingReaderContract;
use FaustVik\Files\Exceptions\FileBaseException;

final class TextSettingReader implements TextSettingReaderContract
{
    public function __construct(
        private readonly bool $isSkipEmptyLine = false,
    ) {
    }

    public function isSkipEmptyLine(): bool
    {
        return $this->isSkipEmptyLine;
    }

    /**
     * @param array<int|string, int|string|bool|float> $text
     * @throws FileBaseException
     * @throws \JsonException
     */
    public function textToString(array $text): string
    {
        if (empty($text)) {
            return '';
        }

        $json = json_encode($text, JSON_THROW_ON_ERROR);
        if (!$json) {
            throw new FileBaseException('Cant json_encode to array');
        }

        return $json;
    }
}

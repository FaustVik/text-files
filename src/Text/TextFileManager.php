<?php

declare(strict_types=1);

namespace FaustVik\Files\Text;

use FaustVik\Files\Contracts\File\FileContract;
use FaustVik\Files\Contracts\File\FileOperationsInterface;
use FaustVik\Files\Contracts\Text\IoTextInterface;
use FaustVik\Files\Contracts\Text\TextSettingReaderContract;
use FaustVik\Files\Dictionary\FileModes;
use FaustVik\Files\Exceptions\File\CantReadFileException;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Exceptions\IsNotResourceException;

final class TextFileManager implements IoTextInterface
{
    public function __construct(
        private readonly FileContract $file,
        private readonly TextSettingReaderContract $settings,
        private readonly FileOperationsInterface $fileOperation,
    ) {
        $this->file->checkFile();
    }

    /**
     * @return array<int, string>
     * @throws IsNotResourceException
     * @throws FileBaseException
     */
    public function readToArray(?int $length = null, ?int $limit = null): array
    {
        /** @var array<int, string> $result */
        $result = $this->withFileHandle(mode: FileModes::ONLY_READ_BINARY, callback: function ($handle) use ($length, $limit): array {
            $result = [];

            $i = 1;
            $isExceedLimit = false;
            while (($line = fgets(stream: $handle, length: $length)) !== false) {
                if ($this->settings->isSkipEmptyLine() && $line === "\n") {
                    continue;
                }
                /** @var string $line */
                $result[] = $line;

                if ($limit !== null && ($limit === $i)) {
                    $isExceedLimit = true;
                    break;
                }

                $i++;
            }

            if ($limit !== null && !$isExceedLimit && !feof(stream: $handle)) {
                throw new CantReadFileException();
            }

            return $result;
        });

        return $result;
    }

    /**
     * @throws FileBaseException
     * @throws CantReadFileException
     * @throws IsNotResourceException
     */
    public function readToString(int $offset = 0, ?int $length = null): string
    {
        /** @var string $content */
        $content = $this->withFileHandle(mode: FileModes::ONLY_READ_BINARY, callback: static function ($handle) use ($length) {
            $result = '';

            while (($buffer = fgets(stream: $handle, length: $length)) !== false) {
                $result .= $buffer;
            }

            if (!feof(stream: $handle)) {
                throw new CantReadFileException();
            }

            return $result;
        });

        if ($offset > 0) {
            $content = substr(string: $content, offset: $offset, length: $length);
        }

        return $content;
    }

    /**
     * @param string|array<int|string, int|string|bool|float> $data
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function overWrite(array|string $data): bool
    {
        return $this->writeData(data: $data, mode: FileModes::WRITE_TRUNC_ONLY);
    }

    /**
     * @param string|array<int|string, int|string|bool|float> $data
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function write(array|string $data): bool
    {
        return $this->writeData(data: $data, mode: FileModes::WRITE_APPEND_ONLY);
    }

    /**
     * @param string|array<int|string, int|string|bool|float> $text
     * @throws CantReadFileException
     * @throws FileBaseException
     * @throws IsNotResourceException
     */
    public function appendToStartFile(array|string $text): bool
    {
        if (\is_array($text)) {
            $text = $this->settings->textToString($text);
        }
        $text .= $this->readToString();

        return $this->overWrite(data: $text);
    }

    /**
     * @param string|array<int|string, int|string|bool|float> $data
     * @param FileModes $mode
     * @return bool
     */
    private function writeData(array|string $data, FileModes $mode): bool
    {
        if (\is_array($data)) {
            $data = $this->settings->textToString($data);
        }

        /** @var false|int $result */
        $result = $this->withFileHandle(mode: $mode, callback: static fn($handle) => fwrite(stream: $handle, data: $data) !== false);
        return !($result === false);
    }

    /**
     * @param FileModes $mode
     * @param callable $callback
     * @return mixed
     * @throws IsNotResourceException
     */
    private function withFileHandle(FileModes $mode, callable $callback): mixed
    {
        $handle = $this->fileOperation->openFile(path: $this->file->getPath(), mode: $mode);

        try {
            return $callback($handle);
        } finally {
            $this->fileOperation->closeFile(handle: $handle);
        }
    }
}

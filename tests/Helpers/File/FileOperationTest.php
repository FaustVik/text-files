<?php

declare(strict_types=1);

namespace FaustVik\Tests\Helpers\File;

use FaustVik\Files\Dictionary\FileModes;
use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Exceptions\IsNotResourceException;
use FaustVik\Files\Helpers\File\FileOperation;
use PHPUnit\Framework\TestCase;

class FileOperationTest extends TestCase
{
    private string $testFile = __DIR__ . '/testfile.txt';
    private string $nonExistentFile = __DIR__ . '/nonexistent.txt';
    private string $destinationFile = __DIR__ . '/destination.txt';

    protected function setUp(): void
    {
        // Создаем тестовый файл
        \file_put_contents($this->testFile, 'Hello, world!');
    }

    protected function tearDown(): void
    {
        // Удаляем тестовые файлы после каждого теста
        foreach ([$this->testFile, $this->destinationFile] as $file) {
            if (\file_exists($file)) {
                \unlink($file);
            }
        }
    }

    public function testDelete(): void
    {
        $this->assertTrue(FileOperation::delete($this->testFile));
        $this->assertFileDoesNotExist($this->testFile);
    }

    public function testDeleteThrowsExceptionIfFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        FileOperation::delete($this->nonExistentFile);
    }

    public function testCopy(): void
    {
        $this->assertTrue(FileOperation::copy($this->testFile, $this->destinationFile));
        $this->assertFileExists($this->destinationFile);
    }

    public function testCopyThrowsExceptionIfSourceFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        FileOperation::copy($this->nonExistentFile, $this->destinationFile);
    }

    public function testMove(): void
    {
        $this->assertTrue(FileOperation::move($this->testFile, $this->destinationFile));
        $this->assertFileExists($this->destinationFile);
        $this->assertFileDoesNotExist($this->testFile);
    }

    public function testRename(): void
    {
        $newPath = FileOperation::rename($this->testFile, 'renamed.txt');
        $this->assertFileExists($newPath);
        $this->assertFileDoesNotExist($this->testFile);
    }

    public function testOpenFile(): void
    {
        $handle = FileOperation::openFile($this->testFile, FileModes::ONLY_READ);
        $this->assertIsResource($handle);
        \fclose($handle);
    }

    public function testOpenFileThrowsExceptionIfFileNotFound(): void
    {
        // Устанавливаем обработчик ошибок, который преобразует предупреждения в исключения
        \set_error_handler(static function ($errno, $errstr) {
            throw new FileBaseException();
        });

        $this->expectException(FileBaseException::class);

        try {
            FileOperation::openFile($this->nonExistentFile, FileModes::ONLY_READ);
        } finally {
            \restore_error_handler();
        }
    }

    public function testCloseFile(): void
    {
        $handle = \fopen($this->testFile, 'r');
        $this->assertTrue(FileOperation::closeFile($handle));
    }

    public function testCloseFileThrowsExceptionIfNotResource(): void
    {
        $this->expectException(IsNotResourceException::class);
        FileOperation::closeFile('not_a_resource');
    }

    public function testFlush(): void
    {
        $this->assertTrue(FileOperation::flush($this->testFile));
        $this->assertStringEqualsFile($this->testFile, '');
    }
}

<?php

declare(strict_types=1);

namespace FaustVik\Tests\File;

use FaustVik\Files\Contracts\File\FileOperationsInterface;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\File\FileCreator;
use FaustVik\Files\Helpers\File\FileInfo;
use PHPUnit\Framework\TestCase;

final class FileCreatorTest extends TestCase
{
    private string $testFile = __DIR__ . '/testfile.txt';
    private FileCreator $fileCreator;
    private FileOperationsInterface $fileOperationsMock;

    protected function setUp(): void
    {
        $this->fileOperationsMock = $this->createMock(FileOperationsInterface::class);
        $this->fileCreator = new FileCreator($this->fileOperationsMock);

        // Удаляем тестовый файл, если он существует
        if (\file_exists($this->testFile)) {
            \unlink($this->testFile);
        }
    }

    protected function tearDown(): void
    {
        // Удаляем тестовый файл после каждого теста
        if (\file_exists($this->testFile)) {
            \unlink($this->testFile);
        }
    }

    public function testCreateNewFile(): void
    {
        // Настраиваем мок
        $this->fileOperationsMock
            ->method('openFile')
            ->willReturn(fopen('php://memory', 'w'));

        $this->fileOperationsMock
            ->method('closeFile')
            ->willReturn(true);

        // Проверяем, что файл не существует
        $this->assertFalse(FileInfo::checkFileExistence($this->testFile));

        // Создаем файл
        $result = $this->fileCreator->create($this->testFile);

        // Проверяем, что файл создан
        $this->assertTrue($result);
    }

    public function testCreateExistingFile(): void
    {
        // Создаем файл вручную
        \file_put_contents($this->testFile, 'test');

        // Проверяем, что файл существует
        $this->assertTrue(FileInfo::checkFileExistence($this->testFile));

        // Пытаемся создать файл еще раз
        $result = $this->fileCreator->create($this->testFile);

        // Проверяем, что метод вернул true и файл не изменился
        $this->assertTrue($result);
        $this->assertStringEqualsFile($this->testFile, 'test');
    }

    public function testCreateFileWithInvalidPath(): void
    {
        // Настраиваем мок, чтобы openFile выбрасывал исключение
        $this->fileOperationsMock
            ->method('openFile')
            ->willThrowException(new FileBaseException('Invalid path'));

        // Ожидаем исключение
        $this->expectException(FileBaseException::class);

        // Пытаемся создать файл
        $this->fileCreator->create('/invalid/path/testfile.txt');
    }

    public function testCloseFileFailure(): void
    {
        // Настраиваем мок
        $this->fileOperationsMock
            ->method('openFile')
            ->willReturn(fopen('php://memory', 'w'));

        $this->fileOperationsMock
            ->method('closeFile')
            ->willReturn(false);

        // Ожидаем исключение
        $this->expectException(FileBaseException::class);
        $this->expectExceptionMessage('Failed to close the file after creation');

        // Пытаемся создать файл
        $this->fileCreator->create($this->testFile);
    }
}

<?php

declare(strict_types=1);

namespace FaustVik\Tests\Helpers\File;

use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Files\Helpers\File\FileInfo;
use PHPUnit\Framework\TestCase;

final class FileInfoTest extends TestCase
{
    private string $testFile = __DIR__ . '/testfile.txt';
    private string $nonExistentFile = __DIR__ . '/nonexistent.txt';

    protected function setUp(): void
    {
        // Создаем тестовый файл
        \file_put_contents($this->testFile, 'Hello, world!');
    }

    protected function tearDown(): void
    {
        // Удаляем тестовый файл после каждого теста
        if (\file_exists($this->testFile)) {
            \unlink($this->testFile);
        }
    }

    public function testCheckFileExistence(): void
    {
        $this->assertTrue(FileInfo::checkFileExistence($this->testFile));
        $this->assertFalse(FileInfo::checkFileExistence($this->nonExistentFile));
    }

    public function testGetName(): void
    {
        $this->assertEquals('testfile.txt', FileInfo::getName($this->testFile));
    }

    public function testGetNameThrowsExceptionIfFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        FileInfo::getName($this->nonExistentFile);
    }

    public function testGetSize(): void
    {
        $this->assertEquals(13, FileInfo::getSize($this->testFile));
    }

    public function testGetSizeThrowsExceptionIfFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        FileInfo::getSize($this->nonExistentFile);
    }

    public function testGetExtension(): void
    {
        $this->assertEquals('txt', FileInfo::getExtension($this->testFile));
    }

    public function testGetExtensionThrowsExceptionIfFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        FileInfo::getExtension($this->nonExistentFile);
    }

    public function testGetFullInfo(): void
    {
        $info = FileInfo::getFullInfo($this->testFile);
        $this->assertArrayHasKey('dirname', $info);
        $this->assertArrayHasKey('basename', $info);
        $this->assertArrayHasKey('extension', $info);
        $this->assertArrayHasKey('filename', $info);
    }

    public function testGetFullInfoThrowsExceptionIfFileNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        FileInfo::getFullInfo($this->nonExistentFile);
    }

    public function testIsWritable(): void
    {
        $this->assertTrue(FileInfo::isWritable($this->testFile));
    }

    public function testIsReadable(): void
    {
        $this->assertTrue(FileInfo::isReadable($this->testFile));
    }

    public function testGetFileOwner(): void
    {
        $owner = FileInfo::getFileOwner($this->testFile);
        $this->assertIsArray($owner);
        $this->assertArrayHasKey('name', $owner);
    }

    public function testGetFileTime(): void
    {
        $this->assertIsInt(FileInfo::getFileTime($this->testFile));
    }

    public function testIsFile(): void
    {
        $this->assertTrue(FileInfo::isFile($this->testFile));
    }
}

<?php

declare(strict_types=1);

namespace FaustVik\Tests\Helpers\File;

use FaustVik\Files\Dictionary\FileModes;
use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\Exceptions\IsNotResourceException;
use FaustVik\Files\Helpers\File\FileOperation;
use FaustVik\Tests\BaseTestCase;

class FileOperationTest extends BaseTestCase
{
    private string $testFile;
    private string $nonExistentFile;
    private string $destinationFile;

    protected function setUp(): void
    {
        $this->testFile = $this->createTempFile('testfile.txt', 'Hello, world!');
        $this->nonExistentFile = $this->getTempPath('nonexistent.txt');
        $this->destinationFile = $this->getTempPath('destination.txt');

        @unlink($this->destinationFile);
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

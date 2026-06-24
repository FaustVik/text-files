<?php

declare(strict_types=1);

namespace FaustVik\Tests\File;

use FaustVik\Files\Contracts\File\FileOperationsInterface;
use FaustVik\Files\Exceptions\FileBaseException;
use FaustVik\Files\File\FileCreator;
use FaustVik\Files\Helpers\File\FileInfo;
use FaustVik\Tests\BaseTestCase;

final class FileCreatorTest extends BaseTestCase
{
    private string $testFile;
    private FileCreator $fileCreator;
    private FileOperationsInterface $fileOperationsMock;

    protected function setUp(): void
    {
        $this->fileOperationsMock = $this->createMock(FileOperationsInterface::class);
        $this->fileCreator = new FileCreator($this->fileOperationsMock);

        $this->testFile = $this->getTempPath('creator_test.txt');
        @unlink($this->testFile);
    }

    public function testCreateNewFile(): void
    {
        $this->fileOperationsMock
            ->method('openFile')
            ->willReturn(fopen('php://memory', 'w'));

        $this->fileOperationsMock
            ->method('closeFile')
            ->willReturn(true);

        $this->assertFalse(FileInfo::checkFileExistence($this->testFile));

        $result = $this->fileCreator->create($this->testFile);

        $this->assertTrue($result);
    }

    public function testCreateExistingFile(): void
    {
        file_put_contents($this->testFile, 'test');

        $this->assertTrue(FileInfo::checkFileExistence($this->testFile));

        $result = $this->fileCreator->create($this->testFile);

        $this->assertTrue($result);
        $this->assertStringEqualsFile($this->testFile, 'test');
    }

    public function testCreateFileWithInvalidPath(): void
    {
        $this->fileOperationsMock
            ->method('openFile')
            ->willThrowException(new FileBaseException('Invalid path'));

        $this->expectException(FileBaseException::class);

        $this->fileCreator->create('/invalid/path/testfile.txt');
    }

    public function testCloseFileFailure(): void
    {
        $this->fileOperationsMock
            ->method('openFile')
            ->willReturn(fopen('php://memory', 'w'));

        $this->fileOperationsMock
            ->method('closeFile')
            ->willReturn(false);

        $this->expectException(FileBaseException::class);
        $this->expectExceptionMessage('Failed to close the file after creation');

        $this->fileCreator->create($this->testFile);
    }
}

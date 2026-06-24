<?php

declare(strict_types=1);

namespace FaustVik\Tests\File;

use FaustVik\Files\Dictionary\FileModes;
use FaustVik\Files\Exceptions\IsNotResourceException;
use FaustVik\Files\File\FileOperationWrapper;
use FaustVik\Tests\BaseTestCase;

final class FileOperationWrapperTest extends BaseTestCase
{
    private string $testFile;

    protected function setUp(): void
    {
        $this->testFile = $this->createTempFile('testfile.txt', 'Hello, world!');
    }

    public function testOpenFile(): void
    {
        $wrapper = new FileOperationWrapper();
        $handle = $wrapper->openFile($this->testFile, FileModes::ONLY_READ);

        $this->assertIsResource($handle);
        fclose($handle);
    }

    public function testOpenFileReadsContent(): void
    {
        $wrapper = new FileOperationWrapper();
        $handle = $wrapper->openFile($this->testFile, FileModes::ONLY_READ_BINARY);

        $content = fread($handle, 1024);
        $wrapper->closeFile($handle);

        $this->assertEquals('Hello, world!', $content);
    }

    public function testCloseFile(): void
    {
        $wrapper = new FileOperationWrapper();
        $handle = fopen($this->testFile, 'r');

        $result = $wrapper->closeFile($handle);

        $this->assertTrue($result);
    }

    public function testCloseFileThrowsIfNotResource(): void
    {
        $this->expectException(IsNotResourceException::class);

        $wrapper = new FileOperationWrapper();
        $wrapper->closeFile('not_a_resource');
    }

    public function testOpenFileWriteMode(): void
    {
        $wrapper = new FileOperationWrapper();
        $handle = $wrapper->openFile($this->testFile, FileModes::WRITE_TRUNC_ONLY);

        fwrite($handle, 'new content');
        $wrapper->closeFile($handle);

        $this->assertEquals('new content', file_get_contents($this->testFile));
    }
}

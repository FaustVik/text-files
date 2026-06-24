<?php

declare(strict_types=1);

namespace FaustVik\Tests;

use FaustVik\Files\Contracts\File\FileContract;
use FaustVik\Files\Contracts\Text\TextSettingReaderContract;
use FaustVik\Files\File\FileOperationWrapper;
use FaustVik\Files\Text\TextFileManager;

final class TextFileManagerTest extends BaseTestCase
{
    private FileContract $mockFile;
    private TextSettingReaderContract $mockSettings;
    private string $tempFileName;

    protected function setUp(): void
    {
        $this->tempFileName = $this->getTempPath('textfile_' . uniqid() . '.txt');

        $this->mockFile = $this->createMock(FileContract::class);
        $this->mockFile->method('getPath')->willReturn($this->tempFileName);

        $this->mockSettings = $this->createMock(TextSettingReaderContract::class);
        $this->mockSettings->method('isSkipEmptyLine')->willReturn(false);
        $this->mockSettings->method('textToString')->willReturnCallback(function ($data) {
            return json_encode($data);
        });
    }

    public function testReadToArray(): void
    {
        file_put_contents($this->tempFileName, "line1\nline2\n");

        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->readToArray();

        $this->assertEquals(["line1\n", "line2\n"], $result);
    }

    public function testReadToArrayWithSkipEmptyLines(): void
    {
        $this->mockSettings->method('isSkipEmptyLine')->willReturn(true);
        file_put_contents($this->tempFileName, "line1\nline2\n");

        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->readToArray();

        $this->assertEquals(["line1\n", "line2\n"], $result);
    }

    public function testReadToString(): void
    {
        file_put_contents($this->tempFileName, "line1\nline2\n");

        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->readToString();

        $this->assertEquals("line1\nline2\n", $result);
    }

    public function testReadToStringWithOffsetAndLength(): void
    {
        file_put_contents($this->tempFileName, "line1\nline2\n");

        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->readToString(6, 5);

        $this->assertEquals("line2", $result);
    }

    public function testOverWrite(): void
    {
        file_put_contents($this->tempFileName, "old content");

        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->overWrite("new content");

        $this->assertTrue($result);
        $this->assertEquals("new content", file_get_contents($this->tempFileName));
    }

    public function testAppendToStartFile(): void
    {
        file_put_contents($this->tempFileName, "original content");

        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->appendToStartFile("prepended content");

        $this->assertTrue($result);
        $this->assertEquals("prepended contentoriginal content", file_get_contents($this->tempFileName));
    }

    public function testWrite(): void
    {
        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->write("appended content");

        $this->assertTrue($result);
        $this->assertEquals("appended content", file_get_contents($this->tempFileName));
    }

    public function testWriteArray(): void
    {
        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->write(['key' => 'value']);

        $this->assertTrue($result);
        $this->assertEquals(json_encode(['key' => 'value']), file_get_contents($this->tempFileName));
    }

    public function testReadToArrayWithLimit(): void
    {
        file_put_contents($this->tempFileName, "line1\nline2\nTexts");

        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->readToArray(limit: 1);

        $this->assertEquals(["line1\n"], $result);
    }

    public function testReadToArrayWithLimitAnd(): void
    {
        file_put_contents($this->tempFileName, "line1\nline2\nTexts");

        $reader = new TextFileManager(file: $this->mockFile, settings: $this->mockSettings, fileOperation: new FileOperationWrapper());
        $result = $reader->readToArray(length: 3, limit: 1);

        $this->assertEquals(["li"], $result);
    }

    public function testFromPathReadToArray(): void
    {
        $path = $this->createTempFile('factory_test.txt', "hello\nworld\n");

        $reader = TextFileManager::fromPath($path);
        $result = $reader->readToArray();

        $this->assertEquals(["hello\n", "world\n"], $result);
    }

    public function testFromPathWithSkipEmptyLines(): void
    {
        $path = $this->createTempFile('factory_skip.txt', "line1\n\nline2\n");

        $reader = TextFileManager::fromPath($path, skipEmptyLines: true);
        $result = $reader->readToArray();

        $this->assertEquals(["line1\n", "line2\n"], $result);
    }

    public function testFromPathWriteAndRead(): void
    {
        $path = $this->createTempFile('factory_rw.txt', '');

        $reader = TextFileManager::fromPath($path);
        $reader->write("data");

        $result = $reader->readToString();
        $this->assertEquals("data", $result);
    }

    public function testFromPathOverWrite(): void
    {
        $path = $this->createTempFile('factory_ow.txt', 'old');

        $reader = TextFileManager::fromPath($path);
        $reader->overWrite('new');

        $result = $reader->readToString();
        $this->assertEquals("new", $result);
    }
}

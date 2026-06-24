<?php

declare(strict_types=1);

namespace FaustVik\Tests\File;

use FaustVik\Files\Csv\CsvFile;
use FaustVik\Files\Exceptions\File\FileExtensionIsNotSupportedException;
use FaustVik\Files\Exceptions\File\FileIsNotReadableException;
use FaustVik\Files\Exceptions\File\FileIsNotWriteableException;
use FaustVik\Files\Exceptions\File\FileNotFoundException;
use FaustVik\Tests\BaseTestCase;

final class CsvFileTest extends BaseTestCase
{
    private string $testFile;

    protected function setUp(): void
    {
        $this->testFile = $this->createTempFile('test.csv', "id,name\n1,John\n");
    }

    public function testGetPath(): void
    {
        $csvFile = new CsvFile($this->testFile);
        $this->assertEquals($this->testFile, $csvFile->getPath());
    }

    public function testGetExtension(): void
    {
        $csvFile = new CsvFile($this->testFile);
        $this->assertEquals('csv', $csvFile->getExtension());
    }

    public function testGetSize(): void
    {
        $csvFile = new CsvFile($this->testFile);
        $this->assertGreaterThan(0, $csvFile->getSize());
    }

    public function testIsCsvExtension(): void
    {
        $csvFile = new CsvFile($this->testFile);
        $this->assertTrue($csvFile->isCsvExtension());
    }

    public function testIsCsvExtensionFalse(): void
    {
        $txtFile = $this->createTempFile('test_' . uniqid() . '.txt', 'hello');

        $csvFile = new CsvFile($txtFile);
        $this->assertFalse($csvFile->isCsvExtension());
    }

    public function testCheckFileSuccess(): void
    {
        $csvFile = new CsvFile($this->testFile);
        $csvFile->checkFile();
        $this->assertTrue(true);
    }

    public function testCheckFileThrowsIfNotFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        $csvFile = new CsvFile('/nonexistent/path.csv');
        $csvFile->checkFile();
    }

    public function testCheckFileThrowsIfNotWritable(): void
    {
        $readOnlyFile = $this->createTempFileWithPermissions(
            'readonly_' . uniqid() . '.csv',
            "id\n1\n",
            0444
        );

        $csvFile = new CsvFile($readOnlyFile);
        $this->expectException(FileIsNotWriteableException::class);
        $csvFile->checkFile();
    }

    public function testCheckFileThrowsIfWrongExtension(): void
    {
        $txtFile = $this->createTempFile('test_' . uniqid() . '.txt', 'hello');

        $csvFile = new CsvFile($txtFile);
        $this->expectException(FileExtensionIsNotSupportedException::class);
        $csvFile->checkFile();
    }

    public function testCheckFileThrowsIfNotReadable(): void
    {
        $noReadFile = $this->createTempFileWithPermissions(
            'noread_' . uniqid() . '.csv',
            "id\n1\n",
            0200
        );

        $csvFile = new CsvFile($noReadFile);
        $this->expectException(FileIsNotReadableException::class);
        $csvFile->checkFile();
    }
}

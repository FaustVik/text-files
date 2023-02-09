<?php

declare(strict_types=1);

namespace FaustVik\Tests;

use FaustVik\Files\Contracts\Csv\CsvSettingReaderContract;
use FaustVik\Files\Contracts\File\CsvFileContract;
use FaustVik\Files\Csv\CsvManager;
use FaustVik\Files\Csv\CsvSettingReader;
use FaustVik\Files\File\FileOperationWrapper;
use PHPUnit\Framework\TestCase;

final class CsvManagerTest extends TestCase
{
    private CsvManager $csvReader;
    private CsvFileContract $mockFile;

    protected function setUp(): void
    {
        $this->mockFile = $this->createMock(CsvFileContract::class);

        $this->mockFile->method('isCsvExtension')->willReturn(true);
        $this->mockFile->method('getPath')->willReturn(__DIR__ . '/test.csv');
    }

    private function setSettingReader(
        string $separator = ',',
        bool $skipFirstLine = false,
        bool $useAssociationForHeader = false,
        string $encoding = 'UTF-8',
        string $escapeChar = '\\',
        string $enclosureChar = '"',
    ): CsvSettingReaderContract {
        return new CsvSettingReader(
            separator: $separator,
            skipFirstLine: $skipFirstLine,
            useAssociationForHeader: $useAssociationForHeader,
            encoding: $encoding, escapeChar: $escapeChar, enclosureChar: $enclosureChar
        );
    }

    private function createCsvManager(CsvSettingReaderContract $setting): void
    {
        $this->csvReader = new CsvManager(file: $this->mockFile, csvSettingReaderContract: $setting, fileOperation: new FileOperationWrapper());
    }

    public function testDeleteColumn(): void
    {
        file_put_contents(__DIR__ . '/test.csv', "id,name,age\n1,John,30\n2,Jane,25");

        $this->createCsvManager($this->setSettingReader());

        $result = $this->csvReader->deleteColumn([1]);

        $this->assertTrue($result);

        $data = file(__DIR__ . '/test.csv');
        $this->assertEquals("id,age\n1,30\n2,25\n", implode('', $data));

        unlink(__DIR__ . '/test.csv');
    }

    public function testDeleteLine(): void
    {
        file_put_contents(__DIR__ . '/test.csv', "id,name,age\n1,John,30\n2,Jane,25\n");
        $this->createCsvManager($this->setSettingReader());

        $result = $this->csvReader->deleteLine([1]);

        $this->assertTrue($result);

        $data = file(__DIR__ . '/test.csv');
        $this->assertEquals("id,name,age\n2,Jane,25\n", implode('', $data));

        unlink(__DIR__ . '/test.csv');
    }

    public function testUpdateHeaders(): void
    {
        file_put_contents(__DIR__ . '/test.csv', "id,name,age\n1,John,30\n2,Jane,25");

        $this->createCsvManager($this->setSettingReader(escapeChar: ''));
        $result = $this->csvReader->updateHeaders(['ID gg', 'Full Name', 'Age']);

        $this->assertTrue($result);

        $data = file(__DIR__ . '/test.csv');

        $expectedContent = "ID gg,Full Name,Age\n1,John,30\n2,Jane,25\n";
        $this->assertEquals($expectedContent, implode('', $data));

        unlink(__DIR__ . '/test.csv');
    }

    public function testGetHeadersColumn(): void
    {
        file_put_contents(__DIR__ . '/test.csv', "id,name,age\n1,John,30\n2,Jane,25\n");

        $this->createCsvManager($this->setSettingReader());

        $headers = $this->csvReader->getHeadersColumn();

        $this->assertEquals(['id', 'name', 'age'], $headers);

        unlink(__DIR__ . '/test.csv');
    }

    public function testWrite(): void
    {
        $fields = [
            ['id' => 1, 'name' => 'John', 'age' => 30],
            ['id' => 2, 'name' => 'Jane', 'age' => 25],
        ];

        $this->createCsvManager($this->setSettingReader());

        $result = $this->csvReader->write($fields);

        $this->assertTrue($result);

        $data = file(__DIR__ . '/test.csv');
        $this->assertEquals("1,John,30\n2,Jane,25\n", implode('', $data));

        unlink(__DIR__ . '/test.csv');
    }

    public function testRead(): void
    {
        file_put_contents(__DIR__ . '/test.csv', "id,name,age\n1,John,30\n2,Jane,25\n");

        $this->createCsvManager($this->setSettingReader());

        $data = $this->csvReader->read();

        $this->assertEquals([
            ['id', 'name', 'age'],
            ['1', 'John', '30'],
            ['2', 'Jane', '25'],
        ], $data);

        unlink(__DIR__ . '/test.csv');
    }
}

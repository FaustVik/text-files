<?php

declare(strict_types=1);

namespace FaustVik\Tests;

use FaustVik\Files\Contracts\Csv\CsvSettingReaderContract;
use FaustVik\Files\Contracts\File\CsvFileContract;
use FaustVik\Files\Csv\CsvManager;
use FaustVik\Files\Csv\CsvSettingReader;
use FaustVik\Files\File\FileOperationWrapper;

final class CsvManagerTest extends BaseTestCase
{
    private CsvManager $csvReader;
    private CsvFileContract $mockFile;

    protected function setUp(): void
    {
        $this->mockFile = $this->createMock(CsvFileContract::class);

        $this->mockFile->method('isCsvExtension')->willReturn(true);
        $this->mockFile->method('getPath')->willReturn($this->getTempPath('test.csv'));

        @unlink($this->getTempPath('test.csv'));
    }

    private function setSettingReader(
        string $separator = ',',
        bool $skipFirstLine = false,
        bool $useAssociationForHeader = false,
        string $escapeChar = '\\',
        string $enclosureChar = '"',
    ): CsvSettingReaderContract {
        return new CsvSettingReader(
            separator: $separator,
            skipFirstLine: $skipFirstLine,
            useAssociationForHeader: $useAssociationForHeader,
            escapeChar: $escapeChar,
            enclosureChar: $enclosureChar
        );
    }

    private function createCsvManager(CsvSettingReaderContract $setting): void
    {
        $this->csvReader = new CsvManager(
            file: $this->mockFile,
            csvSettingReaderContract: $setting,
            fileOperation: new FileOperationWrapper(),
        );
    }

    public function testDeleteColumn(): void
    {
        $this->createTempFile('test.csv', "id,name,age\n1,John,30\n2,Jane,25");

        $this->createCsvManager($this->setSettingReader());

        $result = $this->csvReader->deleteColumn([1]);

        $this->assertTrue($result);

        $data = file($this->getTempPath('test.csv'));
        $this->assertEquals("id,age\n1,30\n2,25\n", implode('', $data));
    }

    public function testDeleteLine(): void
    {
        $this->createTempFile('test.csv', "id,name,age\n1,John,30\n2,Jane,25\n");
        $this->createCsvManager($this->setSettingReader());

        $result = $this->csvReader->deleteLine([1]);

        $this->assertTrue($result);

        $data = file($this->getTempPath('test.csv'));
        $this->assertEquals("id,name,age\n2,Jane,25\n", implode('', $data));
    }

    public function testUpdateHeaders(): void
    {
        $this->createTempFile('test.csv', "id,name,age\n1,John,30\n2,Jane,25");

        $this->createCsvManager($this->setSettingReader(escapeChar: ''));
        $result = $this->csvReader->updateHeaders(['ID gg', 'Full Name', 'Age']);

        $this->assertTrue($result);

        $data = file($this->getTempPath('test.csv'));

        $expectedContent = "ID gg,Full Name,Age\n1,John,30\n2,Jane,25\n";
        $this->assertEquals($expectedContent, implode('', $data));
    }

    public function testGetHeadersColumn(): void
    {
        $this->createTempFile('test.csv', "id,name,age\n1,John,30\n2,Jane,25\n");

        $this->createCsvManager($this->setSettingReader());

        $headers = $this->csvReader->getHeadersColumn();

        $this->assertEquals(['id', 'name', 'age'], $headers);
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

        $data = file($this->getTempPath('test.csv'));
        $this->assertEquals("1,John,30\n2,Jane,25\n", implode('', $data));
    }

    public function testRead(): void
    {
        $this->createTempFile('test.csv', "id,name,age\n1,John,30\n2,Jane,25\n");

        $this->createCsvManager($this->setSettingReader());

        $data = $this->csvReader->read();

        $this->assertEquals([
            ['id', 'name', 'age'],
            ['1', 'John', '30'],
            ['2', 'Jane', '25'],
        ], $data);
    }

    public function testFromPathRead(): void
    {
        $path = $this->createTempFile('factory.csv', "id,name\n1,Alice\n");

        $manager = CsvManager::fromPath($path);
        $data = $manager->read();

        $this->assertEquals([
            ['id', 'name'],
            ['1', 'Alice'],
        ], $data);
    }

    public function testFromPathWithCustomSeparator(): void
    {
        $path = $this->createTempFile('factory_sep.csv', "id;name\n1;Bob\n");

        $manager = CsvManager::fromPath($path, separator: ';');
        $data = $manager->read();

        $this->assertEquals([
            ['id', 'name'],
            ['1', 'Bob'],
        ], $data);
    }

    public function testFromPathWithSkipHeader(): void
    {
        $path = $this->createTempFile('factory_header.csv', "id,name\n1,Charlie\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $data = $manager->read();

        $this->assertEquals([
            ['1', 'Charlie'],
        ], $data);
    }

    public function testFromPathWriteAndRead(): void
    {
        $path = $this->createTempFile('factory_rw.csv', '');

        $manager = CsvManager::fromPath($path);
        $manager->write([['id' => 1, 'name' => 'Dave']]);

        $data = $manager->read();
        $this->assertEquals([['1', 'Dave']], $data);
    }
}

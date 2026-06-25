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

    public function testChunk(): void
    {
        $path = $this->createTempFile('chunk.csv', "id,name\n1,Alice\n2,Bob\n3,Charlie\n4,Diana\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $chunks = [];

        $manager->chunk(2, function (array $chunk) use (&$chunks): true {
            $chunks[] = $chunk;
            return true;
        });

        $this->assertCount(2, $chunks);
        $this->assertEquals([['1', 'Alice'], ['2', 'Bob']], $chunks[0]);
        $this->assertEquals([['3', 'Charlie'], ['4', 'Diana']], $chunks[1]);
    }

    public function testChunkWithRemainder(): void
    {
        $path = $this->createTempFile('chunk_remainder.csv', "id,name\n1,Alice\n2,Bob\n3,Charlie\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $chunks = [];

        $manager->chunk(2, function (array $chunk) use (&$chunks): true {
            $chunks[] = $chunk;
            return true;
        });

        $this->assertCount(2, $chunks);
        $this->assertCount(2, $chunks[0]);
        $this->assertCount(1, $chunks[1]);
    }

    public function testChunkEarlyStop(): void
    {
        $path = $this->createTempFile('chunk_stop.csv', "id,name\n1,Alice\n2,Bob\n3,Charlie\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $chunks = [];

        $manager->chunk(2, function (array $chunk) use (&$chunks): false {
            $chunks[] = $chunk;
            return false;
        });

        $this->assertCount(1, $chunks);
    }

    public function testChunkEmptyFile(): void
    {
        $path = $this->createTempFile('chunk_empty.csv', '');

        $manager = CsvManager::fromPath($path);
        $chunks = [];

        $manager->chunk(10, function (array $chunk) use (&$chunks): true {
            $chunks[] = $chunk;
            return true;
        });

        $this->assertCount(0, $chunks);
    }

    public function testStream(): void
    {
        $path = $this->createTempFile('stream.csv', "id,name\n1,Alice\n2,Bob\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $rows = [];

        $manager->stream(function (array $row) use (&$rows): true {
            $rows[] = $row;
            return true;
        });

        $this->assertEquals([
            ['1', 'Alice'],
            ['2', 'Bob'],
        ], $rows);
    }

    public function testStreamEarlyStop(): void
    {
        $path = $this->createTempFile('stream_stop.csv', "id,name\n1,Alice\n2,Bob\n3,Charlie\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $rows = [];

        $manager->stream(function (array $row) use (&$rows): false {
            $rows[] = $row;
            return false;
        });

        $this->assertCount(1, $rows);
    }

    public function testFilterByColumn(): void
    {
        $path = $this->createTempFile('filter_col.csv', "id,name,age\n1,Alice,30\n2,Bob,25\n3,Charlie,30\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $result = $manager->filterByColumn(2, '30');

        $this->assertCount(2, $result);
        $values = array_values($result);
        $this->assertEquals(['1', 'Alice', '30'], $values[0]);
        $this->assertEquals(['3', 'Charlie', '30'], $values[1]);
    }

    public function testFilterByColumnNoMatch(): void
    {
        $path = $this->createTempFile('filter_nomatch.csv', "id,name\n1,Alice\n2,Bob\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $result = $manager->filterByColumn(1, 'Nobody');

        $this->assertCount(0, $result);
    }

    public function testFilter(): void
    {
        $path = $this->createTempFile('filter.csv', "id,name,age\n1,Alice,30\n2,Bob,25\n3,Charlie,35\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $result = $manager->filter(fn (array $row): bool => (int) $row[2] > 28);

        $this->assertCount(2, $result);
        $values = array_values($result);
        $this->assertEquals(['1', 'Alice', '30'], $values[0]);
        $this->assertEquals(['3', 'Charlie', '35'], $values[1]);
    }

    public function testSearch(): void
    {
        $path = $this->createTempFile('search.csv', "id,name,city\n1,Alice,New York\n2,Bob,Boston\n3,Charlie,New Orleans\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $result = $manager->search('New');

        $this->assertCount(2, $result);
        $values = array_values($result);
        $this->assertEquals(['1', 'Alice', 'New York'], $values[0]);
        $this->assertEquals(['3', 'Charlie', 'New Orleans'], $values[1]);
    }

    public function testSearchSpecificColumn(): void
    {
        $path = $this->createTempFile('search_col.csv', "id,name,city\n1,Alice,New York\n2,Bob,Boston\n3,Charlie,New Orleans\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $result = $manager->search('New', columnIndex: 2);

        $this->assertCount(2, $result);
    }

    public function testSearchCaseInsensitive(): void
    {
        $path = $this->createTempFile('search_case.csv', "id,name\n1,alice\n2,BOB\n3,Charlie\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $result = $manager->search('alice');

        $this->assertCount(1, $result);
        $this->assertEquals(['1', 'alice'], $result[0]);
    }

    public function testSearchNoMatch(): void
    {
        $path = $this->createTempFile('search_none.csv', "id,name\n1,Alice\n2,Bob\n");

        $manager = CsvManager::fromPath($path, skipFirstLine: true);
        $result = $manager->search('NonExistent');

        $this->assertCount(0, $result);
    }
}

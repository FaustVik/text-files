<?php

declare(strict_types=1);

namespace FaustVik\Tests\Helpers\Directory;

use FaustVik\Files\Exceptions\DirectoryExceptionBase;
use FaustVik\Files\Helpers\Directory\DirectoryInfo;
use FaustVik\Files\Helpers\Directory\DirectoryOperation;
use PHPUnit\Framework\TestCase;

final class DirectoryInfoTest extends TestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        // Создаем временную директорию для тестов
        $this->testDir = __DIR__ . '/test_dir';
        if (!file_exists(filename: $this->testDir)) {
            mkdir(directory: $this->testDir);
        }

        // Создаем несколько файлов и поддиректорий для тестов
        file_put_contents(filename: $this->testDir . '/file1.txt', data: '');
        file_put_contents(filename: $this->testDir . '/file2.txt', data: '');
        if (!is_dir(filename: $this->testDir . '/subdir')) {
            mkdir(directory: $this->testDir . '/subdir');
        }
    }

    protected function tearDown(): void
    {
        DirectoryOperation::deleteDir(path: $this->testDir);
    }

    public function testScan(): void
    {
        $result = DirectoryInfo::scan(path: $this->testDir);
        $this->assertContains('file1.txt', $result);
        $this->assertContains('file2.txt', $result);
        $this->assertContains('subdir', $result);
        $this->assertContains('.', $result); // Текущая директория
        $this->assertContains('..', $result); // Родительская директория
    }

    public function testScanWithNonExistentDirectory(): void
    {
        $this->expectException(DirectoryExceptionBase::class);
        $this->expectExceptionMessage('Directory "/non/existent/path" does not exist or is not accessible');

        DirectoryInfo::scan(path: '/non/existent/path');
    }

    public function testIsDirExist(): void
    {
        $this->assertTrue(DirectoryInfo::isDirExist(folder: $this->testDir));
        $this->assertTrue(DirectoryInfo::isDirExist(folder: $this->testDir . '/subdir'));
        $this->assertFalse(DirectoryInfo::isDirExist(folder: $this->testDir . '/non_existent_dir'));
    }

    public function testGetRealPathCaching(): void
    {
        $path = $this->testDir;

        // Первый вызов — кешируем
        $realPath1 = DirectoryInfo::scan(path: $path);

        // Второй вызов — используем кеш
        $realPath2 = DirectoryInfo::scan(path: $path);

        $this->assertEquals($realPath1, $realPath2);
    }
}

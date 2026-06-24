<?php

declare(strict_types=1);

namespace FaustVik\Tests\Helpers\Directory;

use FaustVik\Files\Exceptions\DirectoryExceptionBase;
use FaustVik\Files\Helpers\Directory\DirectoryInfo;
use FaustVik\Tests\BaseTestCase;

final class DirectoryInfoTest extends BaseTestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = $this->createTempDir('dir_info');

        file_put_contents($this->testDir . '/file1.txt', '');
        file_put_contents($this->testDir . '/file2.txt', '');

        if (!is_dir($this->testDir . '/subdir')) {
            mkdir($this->testDir . '/subdir');
        }
    }

    public function testScan(): void
    {
        $result = DirectoryInfo::scan(path: $this->testDir);
        $this->assertContains('file1.txt', $result);
        $this->assertContains('file2.txt', $result);
        $this->assertContains('subdir', $result);
        $this->assertContains('.', $result);
        $this->assertContains('..', $result);
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

        $realPath1 = DirectoryInfo::scan(path: $path);
        $realPath2 = DirectoryInfo::scan(path: $path);

        $this->assertEquals($realPath1, $realPath2);
    }
}

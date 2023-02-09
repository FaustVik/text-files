<?php

declare(strict_types=1);

namespace FaustVik\Tests\Helpers\Directory;

use FaustVik\Files\Exceptions\DirectoryExceptionBase;
use FaustVik\Files\Exceptions\IsNotResourceException;
use FaustVik\Files\Helpers\Directory\DirectoryOperation;
use PHPUnit\Framework\TestCase;

final class DirectoryOperationTest extends TestCase
{
    /** @var string */
    private $testDir;

    protected function setUp(): void
    {
        // Создаем временную директорию для тестов
        $this->testDir = __DIR__ . '/test_dir';
        if (!file_exists($this->testDir)) {
            mkdir($this->testDir);
        }
    }

    protected function tearDown(): void
    {
        DirectoryOperation::deleteDir(path: $this->testDir);
    }

    public function testCreateDir(): void
    {
        $newDir = $this->testDir . '/new_dir';
        DirectoryOperation::createDir(path: $newDir);
        $this->assertDirectoryExists($newDir);
    }

    public function testCreateDirRecursive(): void
    {
        $newDir = $this->testDir . '/nested/dir';
        DirectoryOperation::createDir(path: $newDir, recursive: true);
        $this->assertDirectoryExists($newDir);
    }

    public function testCreateDirFailure(): void
    {
        // Преобразуем предупреждения в исключения
        set_error_handler(static function ($errno, $errstr) {
            throw new \RuntimeException($errstr, $errno);
        }, E_WARNING);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('mkdir(): No such file or directory'); // Ожидаемое системное сообщение

        try {
            DirectoryOperation::createDir(path: '/invalid/path');
        } finally {
            // Восстанавливаем обработчик ошибок
            restore_error_handler();
        }
    }

    public function testDeleteDir(): void
    {
        $newDir = $this->testDir . '/dir_to_delete';
        mkdir($newDir);
        file_put_contents($newDir . '/file.txt', 'test');

        DirectoryOperation::deleteDir(path: $newDir);
        $this->assertDirectoryDoesNotExist($newDir);
    }

    public function testDeleteDirFailure(): void
    {
        $this->expectException(DirectoryExceptionBase::class);
        $this->expectExceptionMessage('Directory "/non/existent/path" does not exist');

        DirectoryOperation::deleteDir(path: '/non/existent/path');
    }

    public function testOpenDir(): void
    {
        $handle = DirectoryOperation::openDir(path: $this->testDir);
        $this->assertIsResource($handle);
        DirectoryOperation::closeDir($handle);
    }

    public function testOpenDirFailure(): void
    {
        $this->expectException(DirectoryExceptionBase::class);
        $this->expectExceptionMessage('Directory "/non/existent/path" does not exist');

        DirectoryOperation::openDir(path: '/non/existent/path');
    }

    public function testCloseDir(): void
    {
        $handle = DirectoryOperation::openDir(path: $this->testDir);
        DirectoryOperation::closeDir($handle);
        $this->assertTrue(true); // Если исключение не выброшено, тест пройден
    }

    public function testCloseDirFailure(): void
    {
        $this->expectException(IsNotResourceException::class);
        DirectoryOperation::closeDir('not_a_resource');
    }
}

<?php

declare(strict_types=1);

namespace FaustVik\Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    private static string $testDir = '';
    private array $createdFiles = [];
    private array $createdDirs = [];

    public static function setUpBeforeClass(): void
    {
        self::$testDir = dirname(__DIR__, 2) . '/.var/test/' . static::class;

        if (!is_dir(self::$testDir)) {
            mkdir(self::$testDir, 0777, true);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (is_dir(self::$testDir)) {
            self::recursiveDelete(self::$testDir);
        }
    }

    protected function getTestDir(): string
    {
        return self::$testDir;
    }

    protected function createTempFile(string $name, string $content = ''): string
    {
        $path = self::$testDir . '/' . $name;
        file_put_contents($path, $content);
        $this->createdFiles[] = $path;

        return $path;
    }

    protected function createTempDir(string $name): string
    {
        $path = self::$testDir . '/' . $name;

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $this->createdDirs[] = $path;

        return $path;
    }

    protected function createTempFileWithPermissions(string $name, string $content, int $permissions): string
    {
        $path = $this->createTempFile($name, $content);
        chmod($path, $permissions);

        return $path;
    }

    protected function getTempPath(string $name): string
    {
        return self::$testDir . '/' . $name;
    }

    private static function recursiveDelete(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                self::recursiveDelete($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}

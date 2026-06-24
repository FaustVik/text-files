<?php

declare(strict_types=1);

namespace FaustVik\Tests\Text;

use FaustVik\Files\Text\TextSettingReader;
use PHPUnit\Framework\TestCase;

final class TextSettingReaderTest extends TestCase
{
    public function testIsSkipEmptyLineDefault(): void
    {
        $reader = new TextSettingReader();
        $this->assertFalse($reader->isSkipEmptyLine());
    }

    public function testIsSkipEmptyLineTrue(): void
    {
        $reader = new TextSettingReader(isSkipEmptyLine: true);
        $this->assertTrue($reader->isSkipEmptyLine());
    }

    public function testTextToStringWithArray(): void
    {
        $reader = new TextSettingReader();
        $result = $reader->textToString(['key' => 'value', 'num' => 42]);

        $this->assertEquals('{"key":"value","num":42}', $result);
    }

    public function testTextToStringWithEmptyArray(): void
    {
        $reader = new TextSettingReader();
        $result = $reader->textToString([]);

        $this->assertEquals('', $result);
    }

    public function testTextToStringWithNestedArray(): void
    {
        $reader = new TextSettingReader();
        $result = $reader->textToString(['a' => [1, 2, 3], 'b' => 'test']);

        $expected = '{"a":[1,2,3],"b":"test"}';
        $this->assertEquals($expected, $result);
    }

    public function testTextToStringWithBoolAndNull(): void
    {
        $reader = new TextSettingReader();
        $result = $reader->textToString(['flag' => true, 'empty' => null, 'num' => 3.14]);

        $expected = '{"flag":true,"empty":null,"num":3.14}';
        $this->assertEquals($expected, $result);
    }

    public function testTextToStringWithSpecialChars(): void
    {
        $reader = new TextSettingReader();
        $result = $reader->textToString(['text' => 'hello "world"']);

        $expected = '{"text":"hello \"world\""}';
        $this->assertEquals($expected, $result);
    }
}

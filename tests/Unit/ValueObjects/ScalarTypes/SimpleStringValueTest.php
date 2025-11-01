<?php

namespace Tests\Unit\ValueObjects\ScalarTypes;

use App\ValueObjects\ScalarTypes\SimpleStringValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SimpleStringValueTest extends TestCase
{
    public static function valueProvider(): array
    {
        return [
            // $value, $str
            ['abc', 'abc'],
            ['', ''],
            [' ', ' '],
            ['', null],
        ];
    }
    
    #[DataProvider('valueProvider')]
    public function test_value(string $value, string|null $str): void
    {
        $this->assertEquals($value, SimpleStringValue::create($str)->value);
    }
}

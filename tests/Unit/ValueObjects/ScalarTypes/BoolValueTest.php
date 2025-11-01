<?php

namespace Tests\Unit\ValueObjects\ScalarTypes;

use App\ValueObjects\ScalarTypes\BoolValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BoolValueTest extends TestCase
{
    public static function trueProvider(): array
    {
        return [
            [true],
            ['on'],
            [1],
        ];
    }
    
    #[DataProvider('trueProvider')]
    public function test_true(mixed $value): void
    {
        $this->assertTrue(BoolValue::create($value)->value);
    }
    
    public static function falseProvider(): array
    {
        return [
            [false],
            [''],
            [0],
            ['false'],
        ];
    }
    
    #[DataProvider('falseProvider')]
    public function test_false(mixed $value): void
    {
        $this->assertFalse(BoolValue::create($value)->value);
    }
}

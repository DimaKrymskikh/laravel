<?php

namespace Tests\Unit\ValueObjects;

use App\Exceptions\RuleException;
use App\ValueObjects\IntValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IntValueTest extends TestCase
{
    public static function correctIntValueProvider(): array
    {
        return [
            [135, '135'],
            [-22, '-22'],
            [12, ' 12    '],
            [PHP_INT_MAX, '77777777777777777777777777777777777777777777777777777777777777777'],
            [-PHP_INT_MAX - 1, '-1111111111111111111111111111111111111111111111111111111111111111'],
        ];
    }
    
    public static function inCorrectIntValueProvider(): array
    {
        return [
            [null],
            [''],
            [' '],
            ['-'],
            ['3-7'],
            ['a'],
            ['h17'],
            ['17dd'],
        ];
    }
    
    #[DataProvider('correctIntValueProvider')]
    public function test_correct_int_value(int $result, ?string $value): void
    {
        $this->assertSame($result, IntValue::create($value)->value);
    }
    
    #[DataProvider('incorrectIntValueProvider')]
    public function test_incorrect_int_value(?string $value): void
    {
        $this->assertSame(0, IntValue::create($value)->value);
    }
    
    #[DataProvider('incorrectIntValueProvider')]
    public function test_incorrect_int_value_with_exception(?string $value): void
    {
        $message = 'Неверное число';
        
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage($message);
        
        IntValue::create($value, 'number', $message);
    }
}

<?php

namespace Tests\Unit\Services\Quiz\Fields\ValueObjects\Quiz;

use App\Exceptions\RuleException;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuizTitleValueTest extends TestCase
{
    public static function correctProvider(): array
    {
        return [
            ['Название опроса'],
        ];
    }
    
    #[DataProvider('correctProvider')]
    public function test_success_value(string $value): void
    {
        $this->assertEquals($value, QuizTitleValue::create($value)->value);
    }
    
    public static function failProvider(): array
    {
        return [
            [null],
            [''],
            // Мало символов
            ['Абвгд'],
            // Много символов
            ['Абвгд12345 Абвгд12345 Абвгд12345 Абвгд12345 Абвгд12345 Абвгд12345 Абвгд12345 Абвгд12345 Абвгд12345 Абвгд12345'],
        ];
    }
    
    #[DataProvider('failProvider')]
    public function test_fail_value(string|null $value): void
    {
        $this->expectException(RuleException::class);
        
        $this->assertEquals('', QuizTitleValue::create($value)->value);
    }
}

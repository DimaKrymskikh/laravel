<?php

namespace Tests\Unit\Services\Quiz\Fields\ValueObjects\Quiz;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizLeadTimeValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuizLeadTimeValueTest extends TestCase
{
    public static function successProvider(): array
    {
        return [
            // $leadTime, $value
            [10, '10'],
            [15, '15x'],
        ];
    }
    
    #[DataProvider('successProvider')]
    public function test_success_value(int $leadTime, string $value): void
    {
        $this->assertEquals($leadTime, QuizLeadTimeValue::create($value)->value);
    }
    
    public static function failProvider(): array
    {
        return [
            // $leadTime, $value
            [0, null],
            [0, ''],
            [0, 'abc'],
            [-5, '-5'],
            [Quiz::DEFAULT_LAED_TIME, '2400'],
        ];
    }
    
    #[DataProvider('failProvider')]
    public function test_fail_value(int $leadTime, string|null $value): void
    {
        $this->expectException(RuleException::class);
        
        $this->assertEquals($leadTime, QuizLeadTimeValue::create($value)->value);
    }
}

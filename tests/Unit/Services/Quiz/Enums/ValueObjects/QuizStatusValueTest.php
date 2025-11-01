<?php

namespace Tests\Unit\Services\Quiz\Enums\ValueObjects;

use App\Exceptions\RuleException;
use App\Services\Quiz\Enums\ValueObjects\QuizStatusValue;
use App\Services\Quiz\Enums\QuizStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuizStatusValueTest extends TestCase
{
    public static function successProvider(): array
    {
        return [
            ['at_work', QuizStatus::AtWork],
            ['ready', QuizStatus::Ready],
            ['removed', QuizStatus::Removed],
            ['approved', QuizStatus::Approved],
        ];
    }
    
    #[DataProvider('successProvider')]
    public function test_success_value(string $value, QuizStatus $status): void
    {
        $this->assertEquals($status, QuizStatusValue::create($value)->status);
    }
    
    public static function failProvider(): array
    {
        return [
            [null],
            [''],
            ['fail'],
        ];
    }
    
    #[DataProvider('failProvider')]
    public function test_fail_value(string|null $value): void
    {
        $this->expectException(RuleException::class);
        
        QuizStatusValue::create($value);
    }
    
    public function test_fail_allowQuizChanges(): void
    {
        $this->expectException(RuleException::class);
        
        QuizStatusValue::create('removed')->allowQuizChanges();
    }
    
    public function test_fail_checkFinalStatus(): void
    {
        $this->expectException(RuleException::class);
        
        QuizStatusValue::create('at_work')->checkFinalStatus();
    }
}

<?php

namespace Tests\Unit\Services\Quiz\Enums\ValueObjects;

use App\Exceptions\RuleException;
use App\Services\Quiz\Enums\ValueObjects\QuizItemStatusValue;
use App\Services\Quiz\Enums\QuizItemStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuizItemStatusValueTest extends TestCase
{
    public static function successProvider(): array
    {
        return [
            ['at_work', QuizItemStatus::AtWork],
            ['ready', QuizItemStatus::Ready],
            ['removed', QuizItemStatus::Removed],
        ];
    }
    
    #[DataProvider('successProvider')]
    public function test_success_value(string $value, QuizItemStatus $status): void
    {
        $this->assertEquals($status, QuizItemStatusValue::create($value)->status);
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
        
        QuizItemStatusValue::create($value);
    }
    
    public function test_fail_allowQuizChanges(): void
    {
        $this->expectException(RuleException::class);
        
        QuizItemStatusValue::create('removed')->allowQuizItemChanges();
    }
    
    public function test_fail_checkFinalStatus(): void
    {
        $this->expectException(RuleException::class);
        
        QuizItemStatusValue::create('at_work')->checkFinalStatus();
    }
}

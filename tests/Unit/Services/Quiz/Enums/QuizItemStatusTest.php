<?php

namespace Tests\Unit\Services\Quiz\Enums;

use App\Exceptions\RuleException;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizItemStatuses\AtWorkStatus;
use App\Services\Quiz\Enums\QuizItemStatuses\ReadyStatus;
use App\Services\Quiz\Enums\QuizItemStatuses\RemovedStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuizItemStatusTest extends TestCase
{
    public function test_success_getInfo(): void
    {
        $this->assertInstanceOf(AtWorkStatus::class, QuizItemStatus::AtWork->getInfo());
        $this->assertInstanceOf(ReadyStatus::class, QuizItemStatus::Ready->getInfo());
        $this->assertInstanceOf(RemovedStatus::class, QuizItemStatus::Removed->getInfo());
    }
    
    public static function successAllowQuizChangesProvider(): array
    {
        return [
            [QuizItemStatus::AtWork],
            [QuizItemStatus::Ready],
        ];
    }
    
    #[DataProvider('successAllowQuizChangesProvider')]
    public function test_success_allowQuizChanges(QuizItemStatus $status): void
    {
        $this->assertTrue($status->allowQuizItemChanges());
    }
    
    public static function failAllowQuizChangesProvider(): array
    {
        return [
            [QuizItemStatus::Removed],
        ];
    }
    
    #[DataProvider('failAllowQuizChangesProvider')]
    public function test_fail_allowQuizChanges(QuizItemStatus $status): void
    {
        $this->expectException(RuleException::class);
        
        $status->allowQuizItemChanges();
    }
    
    public static function successCheckFinalStatusProvider(): array
    {
        return [
            [QuizItemStatus::Removed],
        ];
    }
    
    #[DataProvider('successCheckFinalStatusProvider')]
    public function test_success_checkFinalStatus(QuizItemStatus $status): void
    {
        $this->assertTrue($status->checkFinalStatus());
    }
    
    public static function failCheckFinalStatusProvider(): array
    {
        return [
            [QuizItemStatus::AtWork],
            [QuizItemStatus::Ready],
        ];
    }
    
    #[DataProvider('failCheckFinalStatusProvider')]
    public function test_fail_checkFinalStatus(QuizItemStatus $status): void
    {
        $this->expectException(RuleException::class);
        
        $status->checkFinalStatus();
    }
}

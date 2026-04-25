<?php

namespace Tests\Unit\Services\Quiz\Enums;

use App\Exceptions\RuleException;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Enums\QuizStatuses\ApprovedStatus;
use App\Services\Quiz\Enums\QuizStatuses\AtWorkStatus;
use App\Services\Quiz\Enums\QuizStatuses\ReadyStatus;
use App\Services\Quiz\Enums\QuizStatuses\RemovedStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuizStatusTest extends TestCase
{
    public function test_success_getInfo(): void
    {
        $this->assertInstanceOf(ApprovedStatus::class, QuizStatus::Approved->getInfo());
        $this->assertInstanceOf(AtWorkStatus::class, QuizStatus::AtWork->getInfo());
        $this->assertInstanceOf(ReadyStatus::class, QuizStatus::Ready->getInfo());
        $this->assertInstanceOf(RemovedStatus::class, QuizStatus::Removed->getInfo());
    }
    
    public static function successAllowQuizChangesProvider(): array
    {
        return [
            [QuizStatus::AtWork],
            [QuizStatus::Ready],
        ];
    }
    
    #[DataProvider('successAllowQuizChangesProvider')]
    public function test_success_allowQuizChanges(QuizStatus $status): void
    {
        $this->assertTrue($status->allowQuizChanges());
    }
    
    public static function failAllowQuizChangesProvider(): array
    {
        return [
            [QuizStatus::Approved],
            [QuizStatus::Removed],
        ];
    }
    
    #[DataProvider('failAllowQuizChangesProvider')]
    public function test_fail_allowQuizChanges(QuizStatus $status): void
    {
        $this->expectException(RuleException::class);
        
        $status->allowQuizChanges();
    }
    
    public static function successCheckFinalStatusProvider(): array
    {
        return [
            [QuizStatus::Approved],
            [QuizStatus::Removed],
        ];
    }
    
    #[DataProvider('successCheckFinalStatusProvider')]
    public function test_success_checkFinalStatus(QuizStatus $status): void
    {
        $this->assertTrue($status->checkFinalStatus());
    }
    
    public static function failCheckFinalStatusProvider(): array
    {
        return [
            [QuizStatus::AtWork],
            [QuizStatus::Ready],
        ];
    }
    
    #[DataProvider('failCheckFinalStatusProvider')]
    public function test_fail_checkFinalStatus(QuizStatus $status): void
    {
        $this->expectException(RuleException::class);
        
        $status->checkFinalStatus();
    }
}

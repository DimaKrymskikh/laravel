<?php

namespace Tests\Unit\Services\Quiz\Managers;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Managers\QuizStatusManager;
use Tests\Unit\Services\Quiz\QuizTestCase;

final class QuizStatusManagerTest extends QuizTestCase
{
    public function test_defineNewStatus_status_changed(): void
    {
        $quizStatusManager = $this->getDefaultQuizStatusManager();
        $quizStatusManager->defineNewStatus();
        
        $this->assertTrue($quizStatusManager->checkOldAndNewStatusAreNotEqual());
    }
    
    public function test_defineNewStatus_status_unchanged(): void
    {
        // Имеется вопрос в статусе 'в работе'
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS, 1);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::AtWork);
        
        $quizStatusManager = new QuizStatusManager($quiz);
        $quizStatusManager->defineNewStatus();
        
        $this->assertFalse($quizStatusManager->checkOldAndNewStatusAreNotEqual());
    }
    
    public function test_success_approveNewStatus(): void
    {
        $quizStatusManager = $this->getDefaultQuizStatusManager();
        $quizStatusManager->approveNewStatus(QuizStatus::Removed);
        
        $this->assertEquals(QuizStatus::Removed->value, $quizStatusManager->getNewStatusValue());
    }
    
    public function test_fail_approveNewStatus_automatic_status(): void
    {
        $this->expectException(RuleException::class);
        
        $quizStatusManager = $this->getDefaultQuizStatusManager();
        $quizStatusManager->approveNewStatus(QuizStatus::Ready);
    }
    
    public function test_fail_approveNewStatus_is_not_next_status(): void
    {
        $this->expectException(RuleException::class);
        
        $quizStatusManager = $this->getDefaultQuizStatusManager();
        $quizStatusManager->approveNewStatus(QuizStatus::Approved);
    }
    
    private function getDefaultQuizStatusManager(): QuizStatusManager
    {
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS, 0);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::AtWork);
        
        return new QuizStatusManager($quiz);
    }
}

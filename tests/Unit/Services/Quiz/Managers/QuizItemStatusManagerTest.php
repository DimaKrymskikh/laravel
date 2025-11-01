<?php

namespace Tests\Unit\Services\Quiz\Managers;

use App\Exceptions\RuleException;
use App\Models\Quiz\QuizItem;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Managers\QuizItemStatusManager;
use Tests\Unit\Services\Quiz\QuizTestCase;

final class QuizItemStatusManagerTest extends QuizTestCase
{
    public function test_defineNewStatus_status_changed(): void
    {
        $quizItemStatusManager = $this->getDefaultQuizItemStatusManager();
        $quizItemStatusManager->defineNewStatus();
        
        $this->assertTrue($quizItemStatusManager->checkOldAndNewStatusAreNotEqual());
    }
    
    public function test_defineNewStatus_status_unchanged(): void
    {
        // Нет правильного ответа
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, false);
        $quizItemStatusManager = new QuizItemStatusManager($quizItem);
        
        $quizItemStatusManager->defineNewStatus();
        $this->assertFalse($quizItemStatusManager->checkOldAndNewStatusAreNotEqual());
    }
    
    public function test_success_approveNewStatus(): void
    {
        $quizItemStatusManager = $this->getDefaultQuizItemStatusManager();
        $quizItemStatusManager->approveNewStatus(QuizItemStatus::Removed);
        
        $this->assertEquals(QuizItemStatus::Removed->value, $quizItemStatusManager->getNewStatusValue());
    }
    
    public function test_fail_approveNewStatus_automatic_status(): void
    {
        $this->expectException(RuleException::class);
        
        $quizItemStatusManager = $this->getDefaultQuizItemStatusManager();
        $quizItemStatusManager->approveNewStatus(QuizItemStatus::Ready);
    }
    
    public function test_fail_approveNewStatus_is_not_next_status(): void
    {
        $this->expectException(RuleException::class);
        
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::Removed, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, false);
        $quizItemStatusManager = new QuizItemStatusManager($quizItem);
        $quizItemStatusManager->approveNewStatus(QuizItemStatus::Removed);
    }
    
    private function getDefaultQuizItemStatusManager(): QuizItemStatusManager
    {
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, true);
        
        return new QuizItemStatusManager($quizItem);
    }
}

<?php

namespace Tests\Unit\Services\Quiz\Admin;

use App\Contracts\Services\Quiz\StatusInterface;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizItem;
use App\Services\Quiz\Admin\AdminQuizItemService;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\QuizItemField;
use Tests\Unit\Services\Quiz\QuizTestCase;

final class AdminQuizItemServiceTest extends QuizTestCase
{
    private AdminQuizItemService $quizItemService;

    public function test_success_getQuizItemByIdWithAnswers(): void
    {
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS - 1, true, true, true);
        
        $this->quizItemQueries->method('getByIdWithAnswers')
                ->willReturn($quizItem);
        
        $appQuizItem = $this->quizItemService->getQuizItemByIdWithAnswers($this->quizItemId);
        
        $this->assertInstanceOf(QuizItem::class, $appQuizItem);
        $this->assertInstanceOf(StatusInterface::class, $appQuizItem->status_info);
        $this->assertInstanceOf(StatusInterface::class, $appQuizItem->quiz->status_info);
    }
    
    public function test_success_create(): void
    {
        $dto = $this->getQuizItemDto();
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS - 1, Quiz::MINIMUM_ITEMS_FOR_READY_STATUS - 1);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::AtWork);
        
        $this->quizQueries->method('find')
                ->willReturn($quiz);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save');
        
        $newQuizItem = $this->quizItemService->create($dto);
        
        $this->assertInstanceOf(QuizItem::class, $newQuizItem);
        $this->assertEquals($newQuizItem->quiz_id, $dto->quizId);
        $this->assertEquals($newQuizItem->description, $dto->description->value);
    }
    
    public function test_success_update(): void
    {
        $quizItemField = QuizItemField::create('priority', '8');
        
        $quiz = $this->factoryQuiz();
        $quizItem = $this->factoryQuizItem($quiz, QuizItemStatus::Ready);
        
        $this->quizItemQueries->method('find')
                ->willReturn($quizItem);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save')
                ->with($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->updateField($this->quizItemId, $quizItemField));
    }
    
    public function test_success_changeStatus(): void
    {
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, true, true, true);
        
        $this->quizItemQueries->method('getByIdWithAnswers')
                ->willReturn($quizItem);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save')
                ->with($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->changeStatus($this->quizItemId));
    }
    
    public function test_success_setFinalStatus(): void
    {
        $status = QuizItemStatus::from('removed');
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, true, true, true);
        
        $this->quizItemQueries->method('getByIdWithAnswers')
                ->willReturn($quizItem);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save')
                ->with($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->setFinalStatus($this->quizItemId, $status));
    }
    
    public function test_success_cancelFinalStatus(): void
    {
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::Removed, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, true, true, true);
        
        $this->quizItemQueries->method('getByIdWithAnswers')
                ->willReturn($quizItem);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save')
                ->with($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->cancelFinalStatus($this->quizItemId));
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->quizItemService = new AdminQuizItemService($this->serviceManager);
    }
}

<?php

namespace Tests\Unit\Services\Quiz\Admin;

use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizItem;
use App\Modifiers\Quiz\QuizItemModifiersInterface;
use App\Queries\Quiz\QuizItems\AdminQuizItemQueriesInterface;
use App\Queries\Quiz\Quizzes\AdminQuizQueriesInterface;
use App\Services\Quiz\Admin\AdminQuizItemService;
use App\Services\Quiz\Enums\ValueObjects\QuizItemStatusValue;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\StatusInterface;
use Tests\Unit\Services\Quiz\QuizTestCase;

final class AdminQuizItemServiceTest extends QuizTestCase
{
    private QuizItemModifiersInterface $quizItemModifiers;
    private AdminQuizItemQueriesInterface $quizItemQueries;
    private AdminQuizQueriesInterface $quizQueries;
    private AdminQuizItemService $quizItemService;
    private int $quizItemId = 29;

    public function test_success_getQuizItemByIdWithAnswers(): void
    {
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS - 1, true);
        
        $this->quizItemQueries->expects($this->once())
                ->method('getByIdWithAnswers')
                ->with($this->quizItemId)
                ->willReturn($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->getQuizItemByIdWithAnswers($this->quizItemId));
        $this->assertInstanceOf(StatusInterface::class, $quizItem->status);
        $this->assertInstanceOf(StatusInterface::class, $quizItem->quiz->status);
    }
    
    public function test_success_create(): void
    {
        $dto = $this->getQuizItemDto();
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS - 1, Quiz::MINIMUM_ITEMS_FOR_READY_STATUS - 1);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::AtWork);
        
        $this->quizQueries->expects($this->once())
                ->method('getById')
                ->with($dto->quizId)
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
        $description = $this->getQuizItemDto()->description;
        $quiz = $this->factoryQuiz();
        
        $quizItem = $this->factoryQuizItem($quiz, QuizItemStatus::Ready);
        
        $this->quizItemQueries->expects($this->once())
                ->method('getById')
                ->with($this->quizItemId)
                ->willReturn($quizItem);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save')
                ->with($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->update($description, $this->quizItemId));
    }
    
    public function test_success_changeStatus(): void
    {
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, true);
        
        $this->quizItemQueries->expects($this->once())
                ->method('getByIdWithAnswers')
                ->with($this->quizItemId)
                ->willReturn($quizItem);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save')
                ->with($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->changeStatus($this->quizItemId));
    }
    
    public function test_success_setFinalStatus(): void
    {
        $status = QuizItemStatusValue::create('removed');
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, true);
        
        $this->quizItemQueries->expects($this->once())
                ->method('getByIdWithAnswers')
                ->with($this->quizItemId)
                ->willReturn($quizItem);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save')
                ->with($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->setFinalStatus($this->quizItemId, $status));
    }
    
    public function test_success_cancelFinalStatus(): void
    {
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::Removed, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS, true);
        
        $this->quizItemQueries->expects($this->once())
                ->method('getByIdWithAnswers')
                ->with($this->quizItemId)
                ->willReturn($quizItem);
        
        $this->quizItemModifiers->expects($this->once())
                ->method('save')
                ->with($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizItemService->cancelFinalStatus($this->quizItemId));
    }
    
    protected function setUp(): void
    {
        $this->quizItemModifiers = $this->createMock(QuizItemModifiersInterface::class);
        $this->quizItemQueries = $this->createMock(AdminQuizItemQueriesInterface::class);
        $this->quizQueries = $this->createMock(AdminQuizQueriesInterface::class);
        
        $this->quizItemService = new AdminQuizItemService($this->quizItemModifiers, $this->quizItemQueries, $this->quizQueries);
    }
}

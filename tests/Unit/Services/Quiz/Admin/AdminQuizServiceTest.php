<?php

namespace Tests\Unit\Services\Quiz\Admin;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;
use App\Modifiers\Quiz\QuizModifiersInterface;
use App\Queries\Quiz\Quizzes\AdminQuizQueriesInterface;
use App\Services\Quiz\Admin\AdminQuizService;
use App\Services\Quiz\Enums\ValueObjects\QuizStatusValue;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\StatusInterface;
use App\Support\Collections\Quiz\QuizCollection;
use Tests\Unit\Services\Quiz\QuizTestCase;

final class AdminQuizServiceTest extends QuizTestCase
{
    private QuizModifiersInterface $quizModifiers;
    private AdminQuizQueriesInterface $quizQueries;
    private AdminQuizService $quizService;
    private int $quizId = 17;

    public function test_success_getList(): void
    {
        $quizzes = $this->factoryQuizzes(5);
        
        $this->quizQueries->expects($this->once())
                ->method('getList')
                ->willReturn($quizzes);
        
        $this->assertInstanceOf(QuizCollection::class, $this->quizService->getList());
        foreach ($quizzes as $quiz) {
            $this->assertInstanceOf(StatusInterface::class, $quiz->status);
        }
    }

    public function test_success_getQuizByIdWithQuizItems(): void
    {
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS - 1, 2);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::AtWork);
        
        $this->quizQueries->expects($this->once())
                ->method('getQuizByIdWithQuizItems')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->assertInstanceOf(Quiz::class, $this->quizService->getQuizByIdWithQuizItems($this->quizId));
        foreach ($quiz->quizItems as $item) {
            $this->assertInstanceOf(StatusInterface::class, $item->status);
        }
    }

    public function test_success_create(): void
    {
        $dto = $this->getQuizDto();
        
        $this->quizQueries->expects($this->once())
                ->method('existsByTitle')
                ->with($dto->title)
                ->willReturn(false);
        
        $this->quizModifiers->expects($this->once())
                ->method('save');
        
        $this->assertInstanceOf(Quiz::class, $this->quizService->create($dto));
    }

    public function test_fail_create(): void
    {
        $dto = $this->getQuizDto();
        
        $this->expectException(RuleException::class);
        
        $this->quizQueries->expects($this->once())
                ->method('existsByTitle')
                ->with($dto->title)
                ->willReturn(true);
        
        $this->quizModifiers->expects($this->never())
                ->method('save');
        
        $this->quizService->create($dto);
    }

    public function test_success_updateField(): void
    {
        $oldLeadTime = '10';
        $newLeadTime = '20';
        
        $quiz = $this->factoryQuiz(QuizStatus::AtWork, $oldLeadTime);
        $this->assertEquals($oldLeadTime, $quiz->lead_time);
        
        $this->quizQueries->expects($this->once())
                ->method('getById')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->quizModifiers->expects($this->once())
                ->method('save')
                ->with($quiz);
        
        $this->assertInstanceOf(Quiz::class, $this->quizService->updateField($this->quizId, $this->getQuizField('lead_time', $newLeadTime)));
        // Поле 'lead_time' изменилось.
        $this->assertEquals($newLeadTime, $quiz->lead_time);
    }
    
    public function test_success_changeStatus(): void
    {
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS, 0);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::AtWork);
        
        $this->quizQueries->expects($this->once())
                ->method('getQuizByIdWithQuizItems')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->quizModifiers->expects($this->once())
                ->method('save')
                ->with($quiz);
        
        $this->assertInstanceOf(Quiz::class, $this->quizService->changeStatus($this->quizId));
        $this->assertEquals(QuizStatus::Ready->value, $quiz->status);
    }
    
    public function test_success_setFinalStatus(): void
    {
        $status = QuizStatusValue::create('removed');
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS, 0);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::AtWork);
        
        $this->quizQueries->expects($this->once())
                ->method('getById')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->quizModifiers->expects($this->once())
                ->method('save')
                ->with($quiz);
        
        $this->assertInstanceOf(Quiz::class, $this->quizService->setFinalStatus($status, $this->quizId));
    }
    
    public function test_success_cancelFinalStatus(): void
    {
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS, 0);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::Removed);
        
        $this->quizQueries->expects($this->once())
                ->method('getById')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->quizModifiers->expects($this->once())
                ->method('save')
                ->with($quiz);
        
        $this->assertInstanceOf(Quiz::class, $this->quizService->cancelFinalStatus($this->quizId));
    }
    
    protected function setUp(): void
    {
        $this->quizModifiers = $this->createMock(QuizModifiersInterface::class);
        $this->quizQueries = $this->createMock(AdminQuizQueriesInterface::class);
        
        $this->quizService = new AdminQuizService($this->quizModifiers, $this->quizQueries);
    }
}

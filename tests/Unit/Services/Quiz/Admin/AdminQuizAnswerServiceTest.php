<?php

namespace Tests\Unit\Services\Quiz\Admin;

use App\Models\Quiz\QuizAnswer;
use App\Modifiers\ModifiersInterface;
use App\Queries\Quiz\QuizAnswers\AdminQuizAnswerQueriesInterface;
use App\Queries\Quiz\QuizItems\AdminQuizItemQueriesInterface;
use App\Services\Quiz\Admin\AdminQuizAnswerService;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\QuizAnswerField;
use App\Services\Quiz\StatusInterface;
use Tests\Unit\Services\Quiz\QuizTestCase;

final class AdminQuizAnswerServiceTest extends QuizTestCase
{
    private ModifiersInterface $modifiers;
    private AdminQuizAnswerQueriesInterface $quizAnswerQueries;
    private AdminQuizItemQueriesInterface $quizItemQueries;
    private AdminQuizAnswerService $quizAnswerService;
    private int $quizAnswerId = 11;
    
    public function test_success_getAnswerCard(): void
    {
        $quizAnswer = $this->factoryAnswer(false, QuizStatus::Ready, QuizItemStatus::AtWork);
        
        $this->quizAnswerQueries->expects($this->once())
                ->method('getById')
                ->with($this->quizAnswerId)
                ->willReturn($quizAnswer);
        
        $this->assertInstanceOf(QuizAnswer::class, $this->quizAnswerService->getAnswerCard($this->quizAnswerId));
        $this->assertInstanceOf(StatusInterface::class, $quizAnswer->quizItem->status);
        $this->assertInstanceOf(StatusInterface::class, $quizAnswer->quizItem->quiz->status);
    }
    
    public function test_success_create(): void
    {
        $dto = $this->getQuizAnswerDto();
        $quiz = $this->factoryQuiz();
        $quizItem = $this->factoryQuizItem($quiz);
        
        $this->quizItemQueries->expects($this->once())
                ->method('getById')
                ->with($dto->quizItemId)
                ->willReturn($quizItem);
        
        $this->modifiers->expects($this->once())
                ->method('save');
        
        $newQuizAnswer = $this->quizAnswerService->create($dto);
        
        $this->assertInstanceOf(QuizAnswer::class, $newQuizAnswer);
        $this->assertEquals($newQuizAnswer->quiz_item_id, $dto->quizItemId);
        $this->assertEquals($newQuizAnswer->description, $dto->description->value);
        $this->assertEquals($newQuizAnswer->is_correct, $dto->isCorrect->value);
    }
    
    public function test_success_updateField(): void
    {
        $quizAnswerField = QuizAnswerField::create('is_correct', true);
        
        $quizAnswer = $this->factoryAnswer(false, QuizStatus::Ready, QuizItemStatus::AtWork);
        
        $this->quizAnswerQueries->expects($this->once())
                ->method('getById')
                ->with($this->quizAnswerId)
                ->willReturn($quizAnswer);
        
        $this->quizItemQueries->expects($this->once())
                ->method('getById')
                ->with($quizAnswer->quiz_item_id)
                ->willReturn($quizAnswer->quizItem);
        
        $this->modifiers->expects($this->once())
                ->method('save')
                ->with($quizAnswer);
        
        $this->assertInstanceOf(QuizAnswer::class, $this->quizAnswerService->updateField($this->quizAnswerId, $quizAnswerField));
    }
    
    public function test_success_delete(): void
    {
        $quizAnswer = $this->factoryAnswer(false, QuizStatus::Ready, QuizItemStatus::AtWork);
        
        $this->quizAnswerQueries->expects($this->once())
                ->method('getById')
                ->with($this->quizAnswerId)
                ->willReturn($quizAnswer);
        
        $this->quizItemQueries->expects($this->once())
                ->method('getById')
                ->with($quizAnswer->quiz_item_id)
                ->willReturn($quizAnswer->quizItem);
        
        $this->modifiers->expects($this->once())
                ->method('remove')
                ->with($quizAnswer);
        
        $this->assertEquals($quizAnswer->quiz_item_id, $this->quizAnswerService->delete($this->quizAnswerId));
    }

    protected function setUp(): void
    {
        $this->modifiers = $this->createMock(ModifiersInterface::class);
        $this->quizAnswerQueries = $this->createMock(AdminQuizAnswerQueriesInterface::class);
        $this->quizItemQueries = $this->createMock(AdminQuizItemQueriesInterface::class);
        
        $this->quizAnswerService = new AdminQuizAnswerService($this->modifiers, $this->quizAnswerQueries, $this->quizItemQueries);
    }
}

<?php

namespace Tests\Unit\Services\Quiz\Admin;

use App\Models\Quiz\QuizAnswer;
use App\Modifiers\Quiz\QuizAnswerModifiers;
use App\Queries\Quiz\QuizAnswerQueries;
use App\Queries\Quiz\QuizItemQueries;
use App\Services\Quiz\Admin\AdminQuizAnswerService;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\QuizAnswerField;
use App\Services\Quiz\StatusInterface;
use App\Services\ServiceManagerInterface;
use Tests\Unit\Services\Quiz\QuizTestCase;

final class AdminQuizAnswerServiceTest extends QuizTestCase
{
    private ServiceManagerInterface $serviceManager;
    private QuizAnswerModifiers $quizAnswerModifiers;
    private QuizAnswerQueries $quizAnswerQueries;
    private QuizItemQueries $quizItemQueries;
    private AdminQuizAnswerService $quizAnswerService;
    private int $quizAnswerId = 11;
    
    public function test_success_getAnswerCard(): void
    {
        $quizAnswer = $this->factoryAnswer(false, QuizStatus::Ready, QuizItemStatus::AtWork);
        
        $this->quizAnswerQueries->expects($this->once())
                ->method('getById')
                ->with($this->quizAnswerId)
                ->willReturn($quizAnswer);
        
        $appQuizAnswer = $this->quizAnswerService->getAnswerCard($this->quizAnswerId);
        
        $this->assertInstanceOf(QuizAnswer::class, $appQuizAnswer);
        $this->assertInstanceOf(StatusInterface::class, $appQuizAnswer->quizItem->status_info);
        $this->assertInstanceOf(StatusInterface::class, $appQuizAnswer->quizItem->quiz->status_info);
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
        
        $this->quizAnswerModifiers->expects($this->once())
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
        
        $this->quizAnswerModifiers->expects($this->once())
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
        
        $this->quizAnswerModifiers->expects($this->once())
                ->method('remove')
                ->with($quizAnswer);
        
        $this->assertEquals($quizAnswer->quiz_item_id, $this->quizAnswerService->delete($this->quizAnswerId));
    }

    protected function setUp(): void
    {
        $this->quizAnswerModifiers = $this->createMock(QuizAnswerModifiers::class);
        $this->quizAnswerQueries = $this->createMock(QuizAnswerQueries::class);
        $this->quizItemQueries = $this->createMock(QuizItemQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->quizAnswerModifiers, $this->quizAnswerQueries, $this->quizItemQueries);
        
        $this->quizAnswerService = new AdminQuizAnswerService($this->serviceManager);
    }
}

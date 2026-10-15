<?php

namespace Tests\Unit\Services\Quiz\Admin;

use App\Contracts\Services\Quiz\StatusInterface;
use App\Models\Quiz\QuizAnswer;
use App\Services\Quiz\Admin\AdminQuizAnswerService;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\QuizAnswerField;
use Tests\Unit\Services\Quiz\QuizTestCase;

final class AdminQuizAnswerServiceTest extends QuizTestCase
{
    private AdminQuizAnswerService $quizAnswerService;
    
    public function test_success_getAnswerCard(): void
    {
        $quizAnswer = $this->factoryAnswer(false, QuizStatus::Ready, QuizItemStatus::AtWork);
        
        $this->quizAnswerQueries->method('find')
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
        
        $this->quizItemQueries->method('find')
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
        
        $this->quizAnswerQueries->method('find')
                ->willReturn($quizAnswer);
        
        $this->quizItemQueries->method('find')
                ->willReturn($quizAnswer->quizItem);
        
        $this->quizAnswerModifiers->expects($this->once())
                ->method('save')
                ->with($quizAnswer);
        
        $this->assertInstanceOf(QuizAnswer::class, $this->quizAnswerService->updateField($this->quizAnswerId, $quizAnswerField));
    }
    
    public function test_success_delete(): void
    {
        $quizAnswer = $this->factoryAnswer(false, QuizStatus::Ready, QuizItemStatus::AtWork);
        
        $this->quizAnswerQueries->method('find')
                ->willReturn($quizAnswer);
        
        $this->quizItemQueries->method('find')
                ->willReturn($quizAnswer->quizItem);
        
        $this->quizAnswerModifiers->expects($this->once())
                ->method('remove')
                ->with($quizAnswer);
        
        $this->assertEquals($quizAnswer->quiz_item_id, $this->quizAnswerService->delete($this->quizAnswerId));
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->quizAnswerService = new AdminQuizAnswerService($this->serviceManager);
    }
}

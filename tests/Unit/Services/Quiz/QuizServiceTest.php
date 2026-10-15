<?php

namespace Tests\Unit\Services\Quiz;

use App\Exceptions\DatabaseException;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizAnswer;
use App\Models\Quiz\QuizItem;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\QuizService;

class QuizServiceTest extends QuizTestCase
{
    private QuizService $quizService;

    public function test_success_getQuizById(): void
    {
        $this->quizQueries->method('find')
                ->willReturn(new Quiz());
        
        $this->assertInstanceOf(Quiz::class, $this->quizService->getQuizById($this->quizId));
    }

    public function test_fail_getQuizById(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->quizQueries->method('find')
                ->willReturn(null);
        
        $this->quizService->getQuizById($this->quizId);
    }
    
    public function test_success_getQuizItemById(): void
    {
        $quizItem = $this->factoryQuizItemWithAnswers(QuizItemStatus::AtWork, QuizItem::MINIMUM_ANSWERS_FOR_READY_STATUS - 1, true, true, true);
        
        $this->quizItemQueries->method('find')
                ->willReturn($quizItem);
        
        $this->assertInstanceOf(QuizItem::class, $this->quizService->getQuizItemById($this->quizItemId));
    }

    public function test_fail_getQuizItemById(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->quizItemQueries->method('find')
                ->willReturn(null);
        
        $this->quizService->getQuizItemById($this->quizItemId);
    }

    public function test_success_getQuizAnswerById(): void
    {
        $quizAnswer = $this->factoryAnswer(false, QuizStatus::Ready, QuizItemStatus::AtWork);
        
        $this->quizAnswerQueries->method('find')
                ->willReturn($quizAnswer);
        
        $this->assertInstanceOf(QuizAnswer::class, $this->quizService->getQuizAnswerById($this->quizAnswerId));
    }
    
    public function test_fail_getQuizAnswerById(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->quizAnswerQueries->method('find')
                ->willReturn(null);
        
        $this->quizService->getQuizAnswerById($this->quizAnswerId);
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->quizService = new class($this->serviceManager) extends QuizService {
        };
    }
}

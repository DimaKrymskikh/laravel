<?php

namespace Tests\Unit\Services\Quiz;

use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizAnswer;
use App\Models\Quiz\QuizItem;
use App\Models\Quiz\Trial;
use App\Models\Quiz\TrialAnswer;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Trial\DataTransferObjects\AnswerDto;
use App\Support\Collections\Quiz\QuizItemCollection;
use PHPUnit\Framework\TestCase;

abstract class TrialTestCase extends TestCase
{
    protected function getQuizAnswerDto(User $user, int $id, int $quizAnswerId): AnswerDto
    {
        return new AnswerDto($user, $id, $quizAnswerId);
    }
    
    protected function factoryQuiz(int|string $leadTime = Quiz::DEFAULT_LAED_TIME): Quiz
    {
        return Quiz::factory()
                ->state([
                    'status' => QuizStatus::Approved,
                    'lead_time' => $leadTime,
                ])
                ->make();
    }
    
    protected function factoryQuizWithQuizItems(QuizItemCollection $quizItems): Quiz
    {
        return Quiz::factory()
                ->state([
                    'status' => QuizStatus::Approved,
                    'quizItems' => $quizItems
                ])
                ->make();
    }
    
    protected function factoryQuizItems(int $nQuizItems): QuizItemCollection
    {
        return  QuizItem::factory()
                ->count($nQuizItems)
                ->state([
                    'status' => QuizStatus::Ready->value
                ])
                ->make();
    }
    
    protected function factoryQuizAnswer(): QuizAnswer
    {
        return  QuizAnswer::factory()->make();
    }
    
    protected function factoryTrial(): Trial
    {
        return  Trial::factory()->make();
    }
    
    protected function factoryTrialWithAnswers(int $nAnswers): Trial
    {
        return  Trial::factory()->state([
                    'answers' => TrialAnswer::factory()->count($nAnswers)->make()
                ])->make();
    }
    
    protected function factoryTrialAnswer(): TrialAnswer
    {
        return  TrialAnswer::factory()->make();
    }
}

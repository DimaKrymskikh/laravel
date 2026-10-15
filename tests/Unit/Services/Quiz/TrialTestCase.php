<?php

namespace Tests\Unit\Services\Quiz;

use App\Models\User;
use App\Models\Quiz\QuizAnswer;
use App\Models\Quiz\Trial;
use App\Models\Quiz\TrialAnswer;
use App\Services\Quiz\Trial\DataTransferObjects\AnswerDto;

abstract class TrialTestCase extends QuizTestCase
{
    protected function getAnswerDto(User $user, int $id, int $quizAnswerId): AnswerDto
    {
        return new AnswerDto($user, $id, $quizAnswerId);
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

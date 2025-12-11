<?php

namespace App\Modifiers\Quiz\Facades;

use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\Trial;
use App\Models\Quiz\TrialAnswer;

interface TrialModifiersFacadeInterface
{
    /**
     * Создаёт новую запись или изменяет запись в таблице 'quiz.trials'
     * 
     * @param Trial $trial
     * @return void
     */
    public function saveInTrialTable(Trial $trial): void;
    
    /**
     * Создаёт новую запись или изменяет запись в таблице 'quiz.trial_answers'
     * 
     * @param TrialAnswer $trialAnswer
     * @return void
     */
    public function saveInTrialAnswerTable(TrialAnswer $trialAnswer): void;
    
    /**
     * Записывает в таблицу 'quiz.trials' новый опрос для пользователя и возвращает id этого опроса
     * 
     * @param User $user
     * @param Quiz $quiz
     * @return int
     */
    public function insertInTrialTableGetId(User $user, Quiz $quiz): int;
}

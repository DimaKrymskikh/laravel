<?php

namespace App\Modifiers\Quiz;

use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Modifiers\ModifiersInterface;

interface TrialModifiersInterface extends ModifiersInterface
{
    /**
     * Записывает в таблицу 'quiz.trials' новый опрос для пользователя и возвращает id этого опроса
     * 
     * @param User $user
     * @param Quiz $quiz
     * @return int
     */
    public function insertGetId(User $user, Quiz $quiz): int;
}

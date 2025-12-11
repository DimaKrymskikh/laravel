<?php

namespace App\Queries\Quiz\Trials;

use App\Models\User;
use App\Models\Quiz\Trial;
use App\Queries\QueriesInterface;
use App\Support\Collections\Quiz\TrialCollection;

interface TrialQueriesInterface extends QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'quiz.trials' нет записи с id=%d";
    public const NOT_ACTIVE_TRIAL_FOR_USER = "У пользователя %s нет активного опроса.";
    
    /**
     * Возвращает true, если пользователь уже проходит опрос
     * 
     * @param User $user
     * @return bool
     */
    public function existsActiveTrialByUser(User $user): bool;
    
    /**
     * Получить активный опрос пользователя
     * 
     * @param User $user
     * @return Trial
     */
    public function getActiveTrialByUserWithAnswers(User $user): Trial;
    
    /**
     * Возвращает список пройденных пользователем опросов
     * 
     * @param User $user
     * @return TrialCollection
     */
    public function getListByUserForResults(User $user): TrialCollection;
}

<?php

namespace App\Queries\Quiz\Facades;

use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizAnswer;
use App\Models\Quiz\Trial;
use App\Models\Quiz\TrialAnswer;
use App\Support\Collections\Quiz\QuizCollection;
use App\Support\Collections\Quiz\QuizItemCollection;
use App\Support\Collections\Quiz\TrialCollection;

interface TrialQueriesFacadeInterface
{
    /**
     * Получить из таблицы 'quiz.quiz_answers' запись с первичным ключом id 
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_answers'
     * @return QuizAnswer
     */
    public function getQuizAnswerTableRow(int $id): QuizAnswer;
    
    /**
     * Получить из таблицы 'quiz.trial_answers' запись с первичным ключом id
     * 
     * @param int $id - первичный ключ таблицы 'quiz.trial_answers'
     * @return TrialAnswer
     */
    public function getTrialAnswerTableRow(int $id): TrialAnswer;
    
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
    
    /**
     * По id опроса, который проходит пользователь, получить список вопросов вместе с вариатами ответов
     * 
     * @param int $id - id опроса
     * @return QuizItemCollection
     */
    public function getListQuizItemsForActiveTrial(int $id): QuizItemCollection;
    
    /**
     * Возвращает список опросов в состоянии 'approved' для опроса пользователя
     * 
     * @return QuizCollection
     */
    public function getListQuizzesForTrials(): QuizCollection;
    
    /**
     * Возвращает опрос, который должен пройти пользователь
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuizForTrial(int $id): Quiz;
    
    /**
     * Возвращает опрос, который должен пройти пользователь с вопросами
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuizForTrialWithQuizItems(int $id): Quiz;
}

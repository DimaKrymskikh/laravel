<?php

namespace App\Queries\Quiz\Quizzes;

use App\Models\Quiz\Quiz;
use App\Queries\QueriesInterface;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\Support\Collections\Quiz\QuizCollection;

interface QuizQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.quizzes' нет записи с id=%d";
    
    /**
     * Получить из таблицы 'quiz.quizzes' запись с первичным ключом id
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getById(int $id): Quiz;
    
    /**
     * Получить все ряды таблицы 'quiz.quizzes'
     * 
     * @return QuizCollection
     */
    public function getList(): QuizCollection;
    
    /**
     * Существует ли в таблице 'quiz.quizzes' опрос с названием $title.
     * Если указан параметр $id, то при проверке строка таблицы 'quiz.quizzes' с первичным ключом id пропускается
     * 
     * @param QuizTitleValue $title - название опроса
     * @param int|null $id - id опроса, который должен быть исключён при проверке
     * @return bool
     */
    public function existsByTitle(QuizTitleValue $title, int|null $id = null): bool;
    
    /**
     * По первичному ключу таблицы 'quiz.quizzes' получить опрос с вопросами
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuizByIdWithQuizItems(int $id): Quiz;
    
    /**
     * Возвращает список опросов, которые доступны пользователю (в состоянии 'approved')
     * 
     * @return QuizCollection
     */
    public function getListForTrials(): QuizCollection;
    
    /**
     * Возвращает опрос, который должен пройти пользователь
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getByIdForTrial(int $id): Quiz;
    
    /**
     * Возвращает опрос, который должен пройти пользователь с вопросами
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getByIdForTrialWithQuizItems(int $id): Quiz;
}

<?php

namespace App\Queries\Quiz\QuizItems;

use App\Models\Quiz\QuizItem;
use App\Queries\QueriesInterface;
use App\Support\Collections\Quiz\QuizItemCollection;

interface QuizItemQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.quiz_items' нет записи с id=%d";
    
    /**
     * Получить из таблицы 'quiz.quiz_items' запись с первичным ключом id
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_items'
     * @return QuizItem
     */
    public function getById(int $id): QuizItem;
    
    /**
     * Получить из таблицы 'quiz.quiz_items' запись с первичным ключом id вместе с вариатами ответов
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_items'
     * @return QuizItem
     */
    public function getByIdWithAnswers(int $id): QuizItem;
    
    /**
     * По id опроса, который проходит пользователь, получить список вопросов вместе с вариатами ответов
     * 
     * @param int $quizId - id опроса
     * @return QuizItemCollection
     */
    public function getListByQuizIdWithAnswersForTrial(int $quizId): QuizItemCollection;
}

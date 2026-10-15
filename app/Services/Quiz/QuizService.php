<?php

namespace App\Services\Quiz;

use App\Contracts\Services\ServiceManagerInterface;
use App\Exceptions\DatabaseException;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizAnswer;
use App\Models\Quiz\QuizItem;
use App\Modifiers\Quiz\QuizAnswerModifiers;
use App\Modifiers\Quiz\QuizItemModifiers;
use App\Modifiers\Quiz\QuizModifiers;
use App\Queries\Quiz\QuizAnswerQueries;
use App\Queries\Quiz\QuizItemQueries;
use App\Queries\Quiz\QuizQueries;

abstract class QuizService
{
    const NOT_RECORD_WITH_ID_IN_QUIZ_QUIZZES = "В таблице 'quiz.quizzes' нет записи с id=%d";
    const NOT_RECORD_WITH_ID_IN_QUIZ_QUIZ_ITEMS = "В таблице 'quiz.quiz_items' нет записи с id=%d";
    const NOT_RECORD_WITH_ID_IN_QUIZ_QUIZ_ANSWERS = "В таблице 'quiz.quiz_answers' нет записи с id=%d";
    
    protected QuizAnswerModifiers $quizAnswerModifiers;
    protected QuizItemModifiers $quizItemModifiers;
    protected QuizModifiers $quizModifiers;
    protected QuizAnswerQueries $quizAnswerQueries;
    protected QuizItemQueries $quizItemQueries;
    protected QuizQueries $quizQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        $this->quizAnswerModifiers = $this->serviceManager->getQueriesOrModifiers(QuizAnswerModifiers::class);
        $this->quizItemModifiers = $this->serviceManager->getQueriesOrModifiers(QuizItemModifiers::class);
        $this->quizModifiers = $this->serviceManager->getQueriesOrModifiers(QuizModifiers::class);
        $this->quizAnswerQueries = $this->serviceManager->getQueriesOrModifiers(QuizAnswerQueries::class);
        $this->quizItemQueries = $this->serviceManager->getQueriesOrModifiers(QuizItemQueries::class);
        $this->quizQueries = $this->serviceManager->getQueriesOrModifiers(QuizQueries::class);
    }
    
    /**
     * По id таблицы 'quiz.quizzes' извлекает запись и создаёт ORM-модель.
     * Если в таблице нет записи, будет выброшено исключение DatabaseException.
     * 
     * @param int $id Первичный ключ таблицы 'quiz.quizzes'
     * @return Quiz
     */
    public function getQuizById(int $id): Quiz
    {
        return $this->quizQueries->find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID_IN_QUIZ_QUIZZES, $id));
    }
    
    /**
     * По id таблицы 'quiz.quiz_items' извлекает запись и создаёт ORM-модель.
     * Если в таблице нет записи, будет выброшено исключение DatabaseException.
     * 
     * @param int $id Первичный ключ таблицы 'quiz.quiz_items'
     * @return QuizItem
     */
    public function getQuizItemById(int $id): QuizItem
    {
        return $this->quizItemQueries->find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID_IN_QUIZ_QUIZ_ITEMS, $id));
    }
    
    /**
     * По id таблицы 'quiz.quiz_answers' извлекает запись и создаёт ORM-модель.
     * Если в таблице нет записи, будет выброшено исключение DatabaseException.
     * 
     * @param int $id Первичный ключ таблицы 'quiz.quiz_answers'
     * @return QuizAnswer
     */
    public function getQuizAnswerById(int $id): QuizAnswer
    {
        return $this->quizAnswerQueries->find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID_IN_QUIZ_QUIZ_ANSWERS, $id));
    }
}

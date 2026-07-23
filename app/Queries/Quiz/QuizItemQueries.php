<?php

namespace App\Queries\Quiz;

use App\Exceptions\DatabaseException;
use App\Models\Quiz\QuizItem;
use App\Queries\QueriesInterface;
use App\Support\Collections\Quiz\QuizItemCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizItemQueries implements QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'quiz.quiz_items' нет записи с id=%d";
    
    /**
     * {@inheritDoc} (таблица 'quiz.quiz_items')
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_items'
     * @return bool
     */
    public function exists(int $id): bool
    {
        return QuizItem::where('id', $id)->exists();
    }
    
    /**
     * {@inheritDoc} (таблица 'quiz.quiz_items')
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_items'
     * @return QuizItem
     */
    public function getById(int $id): QuizItem
    {
        return QuizItem::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * Получить из таблицы 'quiz.quiz_items' запись с первичным ключом id вместе с вариатами ответов
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_items'
     * @return QuizItem
     */
    public function getByIdWithAnswers(int $id): QuizItem
    {
        return QuizItem::with([
            'answers' => function (HasMany $query) {
                $query->orderBy('priority');
            }
        ])->find($id);
    }
    
    /**
     * {@inheritDoc} 'quiz.quiz_items'
     * 
     * @return QuizItemCollection
     */
    public function getList(): QuizItemCollection
    {
        return QuizItem::all();
    }
    
    /**
     * По id опроса, который проходит пользователь, получить список вопросов вместе с вариатами ответов
     * 
     * @param int $quizId - id опроса
     * @return QuizItemCollection
     */
    public function getListByQuizIdWithAnswersForTrial(int $quizId): QuizItemCollection
    {
        return QuizItem::where('quiz_id', $quizId)
                ->with([
                    'answers' => function (HasMany $query) {
                        $query->select('id', 'description', 'quiz_item_id')->without('quizItem')->orderBy('priority');
                    }
                ])->get();
    }
}

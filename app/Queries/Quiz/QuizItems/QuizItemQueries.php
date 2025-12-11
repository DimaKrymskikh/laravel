<?php

namespace App\Queries\Quiz\QuizItems;

use App\Exceptions\DatabaseException;
use App\Models\Quiz\QuizItem;
use App\Support\Collections\Quiz\QuizItemCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class QuizItemQueries implements QuizItemQueriesInterface
{
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
     * {@inheritDoc}
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_items'
     * @return QuizItem
     */
    public function getById(int $id): QuizItem
    {
        return QuizItem::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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

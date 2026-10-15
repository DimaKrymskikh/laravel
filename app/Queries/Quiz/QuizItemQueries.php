<?php

namespace App\Queries\Quiz;

use App\Models\Quiz\QuizItem;
use App\Queries\ModelQueries;
use App\Support\Collections\Quiz\QuizItemCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizItemQueries extends ModelQueries
{
    protected function setClassName(): void
    {
        $this->className = QuizItem::class;
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

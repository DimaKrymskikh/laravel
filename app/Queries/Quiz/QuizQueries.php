<?php

namespace App\Queries\Quiz;

use App\Models\Quiz\Quiz;
use App\Queries\ModelQueries;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\Support\Collections\Quiz\QuizCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQueries extends ModelQueries
{
    protected function setClassName(): void
    {
        $this->className = Quiz::class;
    }
    
    /**
     * Существует ли в таблице 'quiz.quizzes' опрос с названием $title.
     * Если указан параметр $id, то при проверке строка таблицы 'quiz.quizzes' с первичным ключом id пропускается
     * 
     * @param QuizTitleValue $title - название опроса
     * @param int|null $id - id опроса, который должен быть исключён при проверке
     * @return bool
     */
    public function existsByTitle(QuizTitleValue $title, int|null $id = null): bool
    {
        return Quiz::where('title', $title->value)
                ->when($id, function (Builder $query, string $id) {
                    $query->where('id', '!=', $id);
                })
                ->exists();
    }
    
    /**
     * По первичному ключу таблицы 'quiz.quizzes' получить опрос с вопросами
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuizByIdWithQuizItems(int $id): Quiz
    {
        return Quiz::with([
            'quizItems' => function (HasMany $query) {
                $query->orderBy('priority');
            }
        ])->find($id);
    }
    
    /**
     * {@inheritDoc} 'quiz.quizzes'
     * 
     * @return QuizCollection
     */
    public function getList(): QuizCollection
    {
        return Quiz::orderBy('title')->get();
    }
    
    /**
     * Возвращает список опросов, которые доступны пользователю (в состоянии 'approved')
     * 
     * @return QuizCollection
     */
    public function getListForTrials(): QuizCollection
    {
        return Quiz::orderBy('title')
                ->where('status', QuizStatus::Approved->value)
                ->get();
    }
    
    /**
     * Возвращает опрос, который должен пройти пользователь
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getByIdForTrial(int $id): Quiz
    {
        return Quiz::where('id', $id)
                ->where('status', QuizStatus::Approved->value)
                ->first() ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * Возвращает опрос, который должен пройти пользователь с вопросами
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getByIdForTrialWithQuizItems(int $id): Quiz
    {
        return Quiz::with([
                    'quizItems' => function (HasMany $query) {
                        $query->where('status', QuizItemStatus::Ready->value);
                }])
                ->where('id', $id)
                ->where('status', QuizStatus::Approved->value)
                ->first() ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
}

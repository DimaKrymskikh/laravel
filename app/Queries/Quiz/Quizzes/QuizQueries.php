<?php

namespace App\Queries\Quiz\Quizzes;

use App\Exceptions\DatabaseException;
use App\Models\Quiz\Quiz;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\Support\Collections\Quiz\QuizCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class QuizQueries implements QuizQueriesInterface
{
    /**
     * {@inheritDoc} (таблица 'quiz.quizzes')
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quizzes'
     * @return bool
     */
    public function exists(int $id): bool
    {
        return Quiz::where('id', $id)->exists();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param QuizTitleValue $title - название опроса
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
     * {@inheritDoc}
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quizzes'
     * @return Quiz
     */
    public function getById(int $id): Quiz
    {
        return Quiz::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     * 
     * @return QuizCollection
     */
    public function getList(): QuizCollection
    {
        return Quiz::orderBy('title')->get();
    }
    
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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

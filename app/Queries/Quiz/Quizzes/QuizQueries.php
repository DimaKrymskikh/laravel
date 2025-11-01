<?php

namespace App\Queries\Quiz\Quizzes;

use App\Exceptions\DatabaseException;
use App\Models\Quiz\Quiz;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\Support\Collections\Quiz\QuizCollection;
use Illuminate\Database\Eloquent\Builder;

abstract class QuizQueries implements QuizQueriesInterface
{
    public function exists(int $id): bool
    {
        return Quiz::where('id', $id)->exists();
    }
    
    public function existsByTitle(QuizTitleValue $title, int|null $id = null): bool
    {
        return Quiz::where('title', $title->value)
                ->when($id, function (Builder $query, string $id) {
                    $query->where('id', '!=', $id);
                })
                ->exists();
    }
    
    public function getById(int $id): Quiz
    {
        return Quiz::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function getList(): QuizCollection
    {
        return Quiz::orderBy('title')->get();
    }
}

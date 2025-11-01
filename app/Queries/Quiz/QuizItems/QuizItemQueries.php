<?php

namespace App\Queries\Quiz\QuizItems;

use App\Exceptions\DatabaseException;
use App\Models\Quiz\QuizItem;
use App\Support\Collections\Quiz\QuizItemCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;

abstract class QuizItemQueries implements QuizItemQueriesInterface
{
    public function exists(int $id): bool
    {
        return QuizItem::where('id', $id)->exists();
    }
    
    public function getById(int $id): QuizItem
    {
        return QuizItem::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function getByIdWithAnswers(int $id): QuizItem
    {
        return QuizItem::with([
            'answers' => function (HasMany $query) {
                $query->orderBy('description');
            }
        ])->find($id);
    }
    
    public function getList(): QuizItemCollection
    {
        return QuizItem::all();
    }
}

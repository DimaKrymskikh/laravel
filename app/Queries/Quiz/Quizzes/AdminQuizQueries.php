<?php

namespace App\Queries\Quiz\Quizzes;

use App\Models\Quiz\Quiz;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class AdminQuizQueries extends QuizQueries implements AdminQuizQueriesInterface
{
    public function getQuizByIdWithQuizItems(int $id): Quiz
    {
        return Quiz::with([
            'quizItems' => function (HasMany $query) {
                $query->orderBy('description');
            }
        ])->find($id);
    }
}

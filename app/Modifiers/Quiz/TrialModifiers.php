<?php

namespace App\Modifiers\Quiz;

use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\Trial;
use App\Modifiers\Modifiers;

final class TrialModifiers extends Modifiers implements TrialModifiersInterface
{
    /**
     * {@inheritDoc}
     * 
     * @param User $user
     * @param Quiz $quiz
     * @return int
     */
    public function insertGetId(User $user, Quiz $quiz): int
    {
        return Trial::insertGetId([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'title' => $quiz->title,
            'lead_time' => $quiz->lead_time,
            'total_quiz_items' => $quiz->quizItems->count(),
        ]);
    }
}

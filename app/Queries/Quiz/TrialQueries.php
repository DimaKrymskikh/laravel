<?php

namespace App\Queries\Quiz;

use App\Models\User;
use App\Models\Quiz\Trial;
use App\Queries\QueriesInterface;
use App\Support\Collections\Quiz\TrialCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrialQueries implements QueriesInterface
{
    public const NOT_RECORD_WITH_ID = "В таблице 'quiz.trials' нет записи с id=%d";
    public const NOT_ACTIVE_TRIAL_FOR_USER = "У пользователя %s нет активного опроса.";
    
    public function exists(int $id): bool
    {
        return Trial::where('id', $id)->exists();
    }
    
    /**
     * Возвращает true, если пользователь уже проходит опрос
     * 
     * @param User $user
     * @return bool
     */
    public function existsActiveTrialByUser(User $user): bool
    {
        return Trial::where('user_id', $user->id)->whereNull('end_at')->exists();
    }
    
    /**
     * {@inheritDoc} (таблица 'quiz.trials')
     * 
     * @param int $id - первичный ключ таблицы
     * @return Trial
     */
    public function getById(int $id): Trial
    {
        return Trial::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * Получить активный опрос пользователя
     * 
     * @param User $user
     * @return Trial
     */
    public function getActiveTrialByUserWithAnswers(User $user): Trial
    {
        return Trial::with([
                    'answers' => function (HasMany $query) {
                        $query->orderBy('priority');
                    }
                ])
                ->where('user_id', $user->id)->whereNull('end_at')->first()
                ?? throw new DatabaseException(sprintf(self::NOT_ACTIVE_TRIAL_FOR_USER, $user->login));
    }
    
    /**
     * {@inheritDoc} 'quiz.trials'
     * 
     * @return TrialCollection
     */
    public function getList(): TrialCollection
    {
        return Trial::orderBy('title')->get();
    }
    
    /**
     * Возвращает список пройденных пользователем опросов
     * 
     * @param User $user
     * @return TrialCollection
     */
    public function getListByUserForResults(User $user): TrialCollection
    {
        return Trial::where('user_id', $user->id)->whereNotNull('end_at')->orderBy('title')->get();
    }
}

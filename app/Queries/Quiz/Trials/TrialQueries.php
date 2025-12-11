<?php

namespace App\Queries\Quiz\Trials;

use App\Models\User;
use App\Models\Quiz\Trial;
use App\Support\Collections\Quiz\TrialCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TrialQueries implements TrialQueriesInterface
{
    public function exists(int $id): bool
    {
        return Trial::where('id', $id)->exists();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param User $user
     * @return bool
     */
    public function existsActiveTrialByUser(User $user): bool
    {
        return Trial::where('user_id', $user->id)->whereNull('end_at')->exists();
    }
    
    public function getById(int $id): Trial
    {
        return Trial::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    /**
     * {@inheritDoc}
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
    
    public function getList(): TrialCollection
    {
        return Trial::orderBy('title')->get();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param User $user
     * @return TrialCollection
     */
    public function getListByUserForResults(User $user): TrialCollection
    {
        return Trial::where('user_id', $user->id)->whereNotNull('end_at')->orderBy('title')->get();
    }
}

<?php

namespace App\Modifiers\Quiz\Facades;

use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\Trial;
use App\Models\Quiz\TrialAnswer;
use App\Modifiers\Quiz\TrialAnswerModifiers;
use App\Modifiers\Quiz\TrialModifiers;

final class TrialModifiersFacade implements TrialModifiersFacadeInterface
{
    private TrialAnswerModifiers $trialAnswerModifiers;
    private TrialModifiers $trialModifiers;
    
    public function __construct()
    {
        $this->trialAnswerModifiers = new TrialAnswerModifiers();
        $this->trialModifiers = new TrialModifiers();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param Trial $trial
     * @return void
     */
    public function saveInTrialTable(Trial $trial): void
    {
        $this->trialModifiers->save($trial);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param TrialAnswer $trialAnswer
     * @return void
     */
    public function saveInTrialAnswerTable(TrialAnswer $trialAnswer): void
    {
        $this->trialAnswerModifiers->save($trialAnswer);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param User $user
     * @param Quiz $quiz
     * @return int
     */
    public function insertInTrialTableGetId(User $user, Quiz $quiz): int
    {
        return $this->trialModifiers->insertGetId($user, $quiz);
    }
}

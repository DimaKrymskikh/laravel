<?php

namespace App\Queries\Quiz\Facades;

use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizAnswer;
use App\Models\Quiz\Trial;
use App\Models\Quiz\TrialAnswer;
use App\Queries\Quiz\QuizAnswers\QuizAnswerQueries;
use App\Queries\Quiz\QuizItems\QuizItemQueries;
use App\Queries\Quiz\Quizzes\QuizQueries;
use App\Queries\Quiz\TrialAnswers\TrialAnswerQueries;
use App\Queries\Quiz\Trials\TrialQueries;
use App\Support\Collections\Quiz\QuizCollection;
use App\Support\Collections\Quiz\QuizItemCollection;
use App\Support\Collections\Quiz\TrialCollection;

final class TrialQueriesFacade implements TrialQueriesFacadeInterface
{
    private QuizAnswerQueries $quizAnswerQueries;
    private QuizItemQueries $quizItemQueries;
    private QuizQueries $quizQueries;
    private TrialAnswerQueries $trialAnswerQueries;
    private TrialQueries $trialQueries;

    public function __construct()
    {
        $this->quizAnswerQueries = new QuizAnswerQueries();
        $this->trialAnswerQueries = new TrialAnswerQueries();
        $this->trialQueries = new TrialQueries();
        $this->quizItemQueries = new QuizItemQueries();
        $this->quizQueries = new QuizQueries();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_answers'
     * @return QuizAnswer
     */
    public function getQuizAnswerTableRow(int $id): QuizAnswer
    {
        return $this->quizAnswerQueries->getById($id);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param int $id - первичный ключ таблицы 'quiz.trial_answers'
     * @return TrialAnswer
     */
    public function getTrialAnswerTableRow(int $id): TrialAnswer
    {
        return $this->trialAnswerQueries->getById($id);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param User $user
     * @return bool
     */
    public function existsActiveTrialByUser(User $user): bool
    {
        return $this->trialQueries->existsActiveTrialByUser($user);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param User $user
     * @return Trial
     */
    public function getActiveTrialByUserWithAnswers(User $user): Trial
    {
        return $this->trialQueries->getActiveTrialByUserWithAnswers($user);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param User $user
     * @return TrialCollection
     */
    public function getListByUserForResults(User $user): TrialCollection
    {
        return $this->trialQueries->getListByUserForResults($user);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param int $id - id опроса
     * @return QuizItemCollection
     */
    public function getListQuizItemsForActiveTrial(int $id): QuizItemCollection
    {
        return $this->quizItemQueries->getListByQuizIdWithAnswersForTrial($id);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @return QuizCollection
     */
    public function getListQuizzesForTrials(): QuizCollection
    {
        return $this->quizQueries->getListForTrials();
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuizForTrial(int $id): Quiz
    {
        return $this->quizQueries->getByIdForTrial($id);
    }
    
    /**
     * {@inheritDoc}
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuizForTrialWithQuizItems(int $id): Quiz
    {
        return $this->quizQueries->getByIdForTrialWithQuizItems($id);
    }
}

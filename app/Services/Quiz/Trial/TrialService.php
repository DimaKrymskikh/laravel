<?php

namespace App\Services\Quiz\Trial;

use App\Exceptions\DatabaseException;
use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\Trial;
use App\Models\Quiz\TrialAnswer;
use App\Modifiers\Quiz\TrialAnswerModifiers;
use App\Modifiers\Quiz\TrialModifiers;
use App\Queries\Quiz\QuizAnswerQueries;
use App\Queries\Quiz\QuizItemQueries;
use App\Queries\Quiz\QuizQueries;
use App\Queries\Quiz\TrialAnswerQueries;
use App\Queries\Quiz\TrialQueries;
use App\Services\Quiz\Trial\DataTransferObjects\AnswerDto;
use App\Services\Quiz\Managers\GradeManager;
use App\Services\ServiceManagerInterface;
use App\Support\Collections\Quiz\QuizCollection;
use App\Support\Collections\Quiz\QuizItemCollection;
use App\Support\Collections\Quiz\TrialCollection;
use Carbon\Carbon;

final class TrialService
{
    private TrialAnswerModifiers $trialAnswerModifiers;
    private TrialModifiers $trialModifiers;
    private QuizAnswerQueries $quizAnswerQueries;
    private QuizItemQueries $quizItemQueries;
    private QuizQueries $quizQueries;
    private TrialAnswerQueries $trialAnswerQueries;
    private TrialQueries $trialQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        $this->trialAnswerModifiers = $this->serviceManager->getQueriesOrModifiers(TrialAnswerModifiers::class);
        $this->trialModifiers = $this->serviceManager->getQueriesOrModifiers(TrialModifiers::class);
        $this->quizAnswerQueries = $this->serviceManager->getQueriesOrModifiers(QuizAnswerQueries::class);
        $this->quizItemQueries = $this->serviceManager->getQueriesOrModifiers(QuizItemQueries::class);
        $this->quizQueries = $this->serviceManager->getQueriesOrModifiers(QuizQueries::class);
        $this->trialAnswerQueries = $this->serviceManager->getQueriesOrModifiers(TrialAnswerQueries::class);
        $this->trialQueries = $this->serviceManager->getQueriesOrModifiers(TrialQueries::class);
    }
    
    /**
     * Возвращает список опросов в состоянии 'approved'
     * 
     * @return QuizCollection
     */
    public function getQuizzes(): QuizCollection
    {
        return $this->quizQueries->getListForTrials();
    }
    
    /**
     * Возвращает опрос, который выбрал или проходит пользователь
     * 
     * @param User $user
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuiz(User $user, int $id): Quiz
    {
        $quiz = $this->quizQueries->getByIdForTrial($id);
        $quiz->isActiveTrial = $this->trialQueries->existsActiveTrialByUser($user);
        
        return $quiz;
    }
    
    /**
     * Создаёт опрос для пользователя
     * 
     * @param User $user
     * @param int $quizId
     * @return void
     * @throws DatabaseException
     */
    public function startTrial(User $user, int $quizId): void
    {
        if($this->trialQueries->existsActiveTrialByUser($user)) {
            throw new DatabaseException(sprintf('У пользователя %s имеется активный опрос. Нельзя начать новый.', $user->login));
        }
        
        $quiz = $this->quizQueries->getByIdForTrialWithQuizItems($quizId);
        
        $trialId = $this->trialModifiers->insertGetId($user, $quiz);
        
        foreach ($quiz->quizItems as $item) {
            $trialAnswer = new TrialAnswer();
            $trialAnswer->trial_id = $trialId;
            $trialAnswer->quiz_item_id = $item->id;
            $trialAnswer->question = $item->description;
            $trialAnswer->priority = $item->priority ?? 0;
            $this->trialAnswerModifiers->save($trialAnswer);
        }
    }
    
    /**
     * Возвращает активный опрос пользователя
     * 
     * @param User $user
     * @return Trial
     */
    public function getActiveTrial(User $user): Trial
    {
        $trial = $this->trialQueries->getActiveTrialByUserWithAnswers($user);
        $trial->start_at_seconds = Carbon::parse($trial->start_at)->timestamp;
        
        return $trial;
    }
    
    /**
     * По id опроса, который проходит пользователь, возвращает список вопросов вместе с вариатами ответов
     * 
     * @param int $id
     * @return QuizItemCollection
     */
    public function getListQuizItemsForActiveTrial(int $id): QuizItemCollection
    {
        return $this->quizItemQueries->getListByQuizIdWithAnswersForTrial($id);
    }
    
    /**
     * Возвращает список пройденных пользователем опросов
     * 
     * @param User $user
     * @return TrialCollection
     */
    public function getTrialsForUserResults(User $user): TrialCollection
    {
        return $this->trialQueries->getListByUserForResults($user);
    }
    
    /**
     * Записывает ответ, данный пользователем, в таблицу 'quiz.trial_answers'
     * 
     * @param AnswerDto $dto
     * @return void
     * @throws DatabaseException
     */
    public function chooseAnswer(AnswerDto $dto): void
    {
        if(!$this->trialQueries->existsActiveTrialByUser($dto->user)) {
            throw new DatabaseException(sprintf('У пользователя %s нет активных опросов. Ответ не принят.', $dto->user->login));
        }
        
        $trial = $this->trialQueries->getActiveTrialByUserWithAnswers($dto->user);
        if($trial->getSecondsUntilQuizEnd() < 0) {
            throw new DatabaseException('Время опроса истекло. Ответ не принят.');
        }
        
        $trialAnswer = $this->trialAnswerQueries->getById($dto->id);
        $answer = $this->quizAnswerQueries->getById($dto->quiz_answer_id);
        
        $trialAnswer->quiz_answer_id = $dto->quiz_answer_id;
        $trialAnswer->answer = $answer->description;
        $trialAnswer->is_correct = $answer->is_correct;
        
        $this->trialAnswerModifiers->save($trialAnswer);
    }
    
    /**
     * Завершает опрос пользователя с подведением итогов
     * 
     * @param User $user
     * @return void
     */
    public function completeTrial(User $user): void
    {
        $trial = $this->trialQueries->getActiveTrialByUserWithAnswers($user);
        $trialAnswers = $trial->answers;
        
        $trial->end_at = Carbon::now('UTC');
        $trial->total_quiz_items = $trialAnswers->count();
        $trial->correct_answers_number = $trialAnswers->filter(fn ($trialAnswer) => $trialAnswer->is_correct === true)->count();
        $trial->grade = GradeManager::find($trial);
        
        $this->trialModifiers->save($trial);
    }
}

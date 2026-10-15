<?php

namespace App\Services\Quiz\Trial;

use App\Contracts\Services\ServiceManagerInterface;
use App\Exceptions\DatabaseException;
use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\Trial;
use App\Models\Quiz\TrialAnswer;
use App\Modifiers\Quiz\TrialAnswerModifiers;
use App\Modifiers\Quiz\TrialModifiers;
use App\Queries\Quiz\TrialAnswerQueries;
use App\Queries\Quiz\TrialQueries;
use App\Services\Quiz\Trial\DataTransferObjects\AnswerDto;
use App\Services\Quiz\Managers\GradeManager;
use App\Services\Quiz\QuizService;
use App\Support\Collections\Quiz\QuizCollection;
use App\Support\Collections\Quiz\QuizItemCollection;
use App\Support\Collections\Quiz\TrialCollection;
use Carbon\Carbon;

final class TrialService extends QuizService
{
    const NOT_RECORD_WITH_ID_QUIZ_TRIAL_ANSWERS = "В таблице 'quiz.trial_answers' нет записи с id=%d";
    
    private TrialAnswerModifiers $trialAnswerModifiers;
    private TrialModifiers $trialModifiers;
    private TrialAnswerQueries $trialAnswerQueries;
    private TrialQueries $trialQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        parent::__construct($serviceManager);
        
        $this->trialAnswerModifiers = $this->serviceManager->getQueriesOrModifiers(TrialAnswerModifiers::class);
        $this->trialModifiers = $this->serviceManager->getQueriesOrModifiers(TrialModifiers::class);
        $this->trialAnswerQueries = $this->serviceManager->getQueriesOrModifiers(TrialAnswerQueries::class);
        $this->trialQueries = $this->serviceManager->getQueriesOrModifiers(TrialQueries::class);
    }
    
    /**
     * По id таблицы 'quiz.trial_answers' извлекает запись и создаёт ORM-модель.
     * Если в таблице нет записи, будет выброшено исключение DatabaseException.
     * 
     * @param int $id Первичный ключ таблицы 'quiz.trial_answers'
     * @return TrialAnswer
     */
    public function getTrialAnswerById(int $id): TrialAnswer
    {
        return $this->trialAnswerQueries->find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID_QUIZ_TRIAL_ANSWERS, $id));
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
        
        $trialAnswer = $this->getTrialAnswerById($dto->id);
        $answer = $this->getQuizAnswerById($dto->quiz_answer_id);
        
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

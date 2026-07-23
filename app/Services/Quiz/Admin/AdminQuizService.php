<?php

namespace App\Services\Quiz\Admin;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizItem;
use App\Modifiers\Quiz\QuizModifiers;
use App\Queries\Quiz\QuizQueries;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\DataTransferObjects\QuizDto;
use App\Services\Quiz\Fields\QuizField;
use App\Services\Quiz\Managers\QuizStatusManager;
use App\Services\Quiz\StatusInterface;
use App\Services\ServiceManagerInterface;
use App\Support\Collections\Quiz\QuizCollection;

final class AdminQuizService
{
    private QuizModifiers $quizModifiers;
    private QuizQueries $quizQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        $this->quizModifiers = $this->serviceManager->getQueriesOrModifiers(QuizModifiers::class);
        $this->quizQueries = $this->serviceManager->getQueriesOrModifiers(QuizQueries::class);
    }
    
    /**
     * Возвращает список опросов
     * 
     * @return QuizCollection
     */
    public function getList(): QuizCollection
    {
        $quizzes = $this->quizQueries->getList();
        
        return $quizzes->each(fn (Quiz $quiz): StatusInterface => $quiz->status_info = $quiz->status->getInfo());
    }
    
    /**
     * Возвращает опрос с вопросами для карточки опроса
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuizByIdWithQuizItems(int $id): Quiz
    {
        $quiz = $this->quizQueries->getQuizByIdWithQuizItems($id);
        $quiz->status_info = $quiz->status->getInfo();
        
        $quiz->quizItems->each(fn (QuizItem $quizItem): StatusInterface => $quizItem->status_info = $quizItem->status->getInfo());
        
        return $quiz;
    }
    
    /**
     * Сохраняет новый опрос в таблице 'quiz.quizzes'
     * 
     * @param QuizDto $dto
     * @return Quiz
     * @throws RuleException
     */
    public function create(QuizDto $dto): Quiz
    {
        $title = $dto->title;
        
        if($this->quizQueries->existsByTitle($title)) {
            throw new RuleException('title', sprintf(QuizField::FAIL_TITLE_MESSAGE, $title->value));
        }
        
        $quiz = new Quiz();
        $quiz->title = $dto->title->value;
        $quiz->description = $dto->description->value;
        $quiz->lead_time = $dto->leadTime->value;
        
        $this->quizModifiers->save($quiz);
        
        return $quiz;
    }
    
    /**
     * Изменяет одно поле опроса
     * 
     * @param int $id - id опроса
     * @param QuizField $quizField
     * @return Quiz
     */
    public function updateField(int $id, QuizField $quizField): Quiz
    {
        $quiz = $this->quizQueries->getById($id);
        $quiz->status->allowQuizChanges();
        
        // Имя метода класса не чувствительно к регистру 
        $fnExists = 'existsBy'.$quizField->field;
        if (method_exists($this->quizQueries::class, $fnExists)) {
            $quizField->checkFieldUniqueness($this->quizQueries->$fnExists($quizField->value, $id));
        }
        
        $field = $quizField->field;
        $quiz->$field = $quizField->value->value;
        
        $this->quizModifiers->save($quiz);
        
        return $quiz;
    }
    
    /**
     * Изменяет статус опроса в автоматическом режиме.
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function changeStatus(int $id): Quiz
    {
        $quiz = $this->quizQueries->getQuizByIdWithQuizItems($id);
        $quizStatusManager = new QuizStatusManager($quiz);
        $quizStatusManager->defineNewStatus();
        
        if ($quizStatusManager->checkOldAndNewStatusAreNotEqual()) {
            $quiz->status = $quizStatusManager->getNewStatus();
            $this->quizModifiers->save($quiz);
        }
        
        return $quiz;
    }
    
    /**
     * Задаёт статус опроса ручным управлением
     * 
     * @param QuizStatus $newStatus
     * @param int $id - id опроса
     * @return Quiz
     */
    public function setFinalStatus(QuizStatus $newStatus, int $id): Quiz
    {
        $quiz = $this->quizQueries->getById($id);
        $quizStatusManager = new QuizStatusManager($quiz);
        $quizStatusManager->approveNewStatus($newStatus);
        
        $quiz->status = $quizStatusManager->getNewStatus();
        $this->quizModifiers->save($quiz);
        
        return $quiz;
    }
    
    /**
     * Отменяет статус опроса ручным управлением
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function cancelFinalStatus(int $id): Quiz
    {
        $quiz = $this->quizQueries->getById($id);
        $quiz->status->checkFinalStatus();
        
        $quizStatusManager = new QuizStatusManager($quiz);
        $quizStatusManager->defineNewStatus();
        
        $quiz->status = $quizStatusManager->getNewStatus();
        $this->quizModifiers->save($quiz);
        
        return $quiz;
    }
}

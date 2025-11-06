<?php

namespace App\Services\Quiz\Admin;

use App\Exceptions\RuleException;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizItem;
use App\Modifiers\Quiz\QuizModifiersInterface;
use App\Queries\Quiz\Quizzes\AdminQuizQueriesInterface;
use App\Services\Quiz\Enums\ValueObjects\QuizStatusValue;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Fields\DataTransferObjects\QuizDto;
use App\Services\Quiz\Fields\QuizField;
use App\Services\Quiz\Managers\QuizStatusManager;
use App\Support\Collections\Quiz\QuizCollection;

final class AdminQuizService
{
    const FAIL_TITLE_MESSAGE = 'Название опроса "%s" уже существует в базе данных. Внесите изменение в название.';
    
    public function __construct(
            private QuizModifiersInterface $quizModifiers,
            private AdminQuizQueriesInterface $adminQuizQueries,
    ) {
    }
    
    /**
     * Возвращает список опросов
     * 
     * @return QuizCollection
     */
    public function getList(): QuizCollection
    {
        $quizzes = $this->adminQuizQueries->getList();
        
        return $quizzes->each(function (Quiz $quiz) {
            return $quiz->status = QuizStatus::from($quiz->status)->getInfo();
        });
    }
    
    /**
     * Возвращает список опросов с вопросами для карточки опроса
     * 
     * @param int $id - id опроса
     * @return Quiz
     */
    public function getQuizByIdWithQuizItems(int $id): Quiz
    {
        $quiz = $this->adminQuizQueries->getQuizByIdWithQuizItems($id);
        $quiz->status = QuizStatus::from($quiz->status)->getInfo();
        
        $quiz->quizItems->each(function (QuizItem $quizItem) {
            return $quizItem->status = QuizItemStatus::from($quizItem->status)->getInfo();
        });
        
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
        
        if($this->adminQuizQueries->existsByTitle($title)) {
            throw new RuleException('title', sprintf(self::FAIL_TITLE_MESSAGE, $title->value));
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
        $quiz = $this->adminQuizQueries->getById($id);
        
        $field = $quizField->field;
        $quiz->$field = $quizField->value;
        
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
        $quiz = $this->adminQuizQueries->getQuizByIdWithQuizItems($id);
        $quizStatusManager = new QuizStatusManager($quiz);
        $quizStatusManager->defineNewStatus();
        
        if ($quizStatusManager->checkOldAndNewStatusAreNotEqual()) {
            $quiz->status = $quizStatusManager->getNewStatusValue();
            $this->quizModifiers->save($quiz);
        }
        
        return $quiz;
    }
    
    /**
     * Задаёт статус опроса ручным управлением
     * 
     * @param QuizStatusValue $newStatus
     * @param int $id - id опроса
     * @return Quiz
     */
    public function setFinalStatus(QuizStatusValue $newStatus, int $id): Quiz
    {
        $quiz = $this->adminQuizQueries->getById($id);
        $quizStatusManager = new QuizStatusManager($quiz);
        $quizStatusManager->approveNewStatus($newStatus->status);
        
        $quiz->status = $quizStatusManager->getNewStatusValue();
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
        $quiz = $this->adminQuizQueries->getById($id);
        QuizStatusValue::create($quiz->status)->checkFinalStatus();
        
        $quizStatusManager = new QuizStatusManager($quiz);
        $quizStatusManager->defineNewStatus();
        
        $quiz->status = $quizStatusManager->getNewStatusValue();
        $this->quizModifiers->save($quiz);
        
        return $quiz;
    }
}

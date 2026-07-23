<?php

namespace App\Services\Quiz\Admin;

use App\Models\Quiz\QuizItem;
use App\Modifiers\Quiz\QuizItemModifiers;
use App\Queries\Quiz\QuizItemQueries;
use App\Queries\Quiz\QuizQueries;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\DataTransferObjects\QuizItemDto;
use App\Services\Quiz\Fields\QuizItemField;
use App\Services\Quiz\Managers\QuizItemStatusManager;
use App\Services\ServiceManagerInterface;

final class AdminQuizItemService
{
    private QuizItemModifiers $quizItemModifiers;
    private QuizItemQueries $quizItemQueries;
    private QuizQueries $quizQueries;
    
    public function __construct(
            private ServiceManagerInterface $serviceManager,
    ) {
        $this->quizItemModifiers = $this->serviceManager->getQueriesOrModifiers(QuizItemModifiers::class);
        $this->quizItemQueries = $this->serviceManager->getQueriesOrModifiers(QuizItemQueries::class);
        $this->quizQueries = $this->serviceManager->getQueriesOrModifiers(QuizQueries::class);
    }
    
    /**
     * Возвращает данные вопроса с ответами для карточки вопроса
     * 
     * @param int $id - первичный ключ таблицы 'quiz.quiz_items'
     * @return QuizItem
     */
    public function getQuizItemByIdWithAnswers(int $id): QuizItem
    {
        $quizItem = $this->quizItemQueries->getByIdWithAnswers($id);
        // Статусы получают данные, необходимые для отрисовки
        $quizItem->status_info = $quizItem->status->getInfo();
        $quizItem->quiz->status_info = $quizItem->quiz->status->getInfo();
        
        return $quizItem;
    }
    
    /**
     * Сохраняет новый вопрос в базе.
     * 
     * @param QuizItemDto $dto
     * @return QuizItem
     */
    public function create(QuizItemDto $dto): QuizItem
    {
        $quiz = $this->quizQueries->getById($dto->quizId);
        $quiz->status->allowQuizChanges();
        
        $quizItem = new QuizItem();
        $quizItem->quiz_id = $dto->quizId;
        $quizItem->description = $dto->description->value;
        
        $this->quizItemModifiers->save($quizItem);
        
        return $quizItem;
    }
    
    /**
     * Изменяет одно поле ответа в таблице 'quiz.quiz_items'.
     * Возвращает изменённый вопрос
     * 
     * @param int $id - id вопроса
     * @param QuizItemField $quizItemField
     * @return QuizItem
     */
    public function updateField(int $id, QuizItemField $quizItemField): QuizItem
    {
        $quizItem = $this->quizItemQueries->getById($id);
        $this->checkQuizItemEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $field = $quizItemField->field;
        $quizItem->$field = $quizItemField->value->value;
        
        $this->quizItemModifiers->save($quizItem);
        
        return $quizItem;
    }
    
    /**
     * Пересчитывает статус вопроса (автоматически)
     * 
     * @param int $id - id вопроса
     * @return QuizItem
     */
    public function changeStatus(int $id): QuizItem
    {
        $quizItem = $this->quizItemQueries->getByIdWithAnswers($id);
        $this->checkQuizItemEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $manager = new QuizItemStatusManager($quizItem);
        $manager->defineNewStatus();
        
        if ($manager->checkOldAndNewStatusAreNotEqual()) {
            $quizItem->status = $manager->getNewStatus()->value;
            $this->quizItemModifiers->save($quizItem);
        }
        
        return $quizItem;
    }
    
    /**
     * Изменяет статус вопроса ручным управлением
     * 
     * @param int $id - id вопроса
     * @param QuizItemStatus $newStatus
     * @return QuizItem
     */
    public function setFinalStatus(int $id, QuizItemStatus $newStatus): QuizItem
    {
        $quizItem = $this->quizItemQueries->getByIdWithAnswers($id);
        $this->checkQuizItemEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $manager = new QuizItemStatusManager($quizItem);
        $manager->approveNewStatus($newStatus);
        
        $quizItem->status = $manager->getNewStatus()->value;
        $this->quizItemModifiers->save($quizItem);
        
        return $quizItem;
    }
    
    /**
     * Отменяет статус вопроса ручным управлением
     * 
     * @param int $id - id вопроса
     * @return QuizItem
     */
    public function cancelFinalStatus(int $id): QuizItem
    {
        $quizItem = $this->quizItemQueries->getByIdWithAnswers($id);
        $quizItem->status->checkFinalStatus();
        
        $manager = new QuizItemStatusManager($quizItem);
        $manager->defineNewStatus();
        
        $quizItem->status = $manager->getNewStatus()->value;
        $this->quizItemModifiers->save($quizItem);
        
        return $quizItem;
    }
    
    /**
     * Проверяет возможность редактирования вопроса.
     * 
     * @param QuizStatus $quizStatus
     * @param QuizItemStatus $quizItemStatus
     * @return void
     */
    private function checkQuizItemEditabilityByStatuses(QuizStatus $quizStatus, QuizItemStatus $quizItemStatus): void
    {
        $quizStatus->allowQuizChanges();
        $quizItemStatus->allowQuizItemChanges();
    }
}

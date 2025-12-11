<?php

namespace App\Services\Quiz\Admin;

use App\Models\Quiz\QuizItem;
use App\Modifiers\Quiz\QuizItemModifiersInterface;
use App\Queries\Quiz\QuizItems\QuizItemQueriesInterface;
use App\Queries\Quiz\Quizzes\QuizQueriesInterface;
use App\Services\Quiz\Enums\ValueObjects\QuizItemStatusValue;
use App\Services\Quiz\Enums\ValueObjects\QuizStatusValue;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\DataTransferObjects\QuizItemDto;
use App\Services\Quiz\Fields\QuizItemField;
use App\Services\Quiz\Managers\QuizItemStatusManager;

final class AdminQuizItemService
{
    public function __construct(
            private QuizItemModifiersInterface $quizItemModifiers,
            private QuizItemQueriesInterface $quizItemQueries,
            private QuizQueriesInterface $quizQueries,
    ) {
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
        $quizItem->status = QuizItemStatus::from($quizItem->status)->getInfo();
        $quizItem->quiz->status = QuizStatus::from($quizItem->quiz->status)->getInfo();
        
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
        QuizStatusValue::create($quiz->status)->allowQuizChanges();
        
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
            $quizItem->status = $manager->getNewStatusValue();
            $this->quizItemModifiers->save($quizItem);
        }
        
        return $quizItem;
    }
    
    /**
     * Изменяет статус вопроса ручным управлением
     * 
     * @param int $id - id вопроса
     * @param QuizItemStatusValue $newStatus
     * @return QuizItem
     */
    public function setFinalStatus(int $id, QuizItemStatusValue $newStatus): QuizItem
    {
        $quizItem = $this->quizItemQueries->getByIdWithAnswers($id);
        $this->checkQuizItemEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $manager = new QuizItemStatusManager($quizItem);
        $manager->approveNewStatus($newStatus->status);
        
        $quizItem->status = $manager->getNewStatusValue();
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
        QuizItemStatusValue::create($quizItem->status)->checkFinalStatus();
        
        $manager = new QuizItemStatusManager($quizItem);
        $manager->defineNewStatus();
        
        $quizItem->status = $manager->getNewStatusValue();
        $this->quizItemModifiers->save($quizItem);
        
        return $quizItem;
    }
    
    /**
     * Проверяет возможность редактирования вопроса.
     * 
     * @param string $quizStatus - статус опроса
     * @param string $quizItemStatus - статус вопроса
     * @return void
     */
    private function checkQuizItemEditabilityByStatuses(string $quizStatus, string $quizItemStatus): void
    {
        QuizStatusValue::create($quizStatus)->allowQuizChanges();
        QuizItemStatusValue::create($quizItemStatus)->allowQuizItemChanges();
    }
}

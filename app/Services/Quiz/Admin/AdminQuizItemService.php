<?php

namespace App\Services\Quiz\Admin;

use App\Models\Quiz\QuizItem;
use App\Modifiers\ModifiersInterface;
use App\Queries\Quiz\QuizItems\AdminQuizItemQueriesInterface;
use App\Queries\Quiz\Quizzes\AdminQuizQueriesInterface;
use App\Services\Quiz\Enums\ValueObjects\QuizItemStatusValue;
use App\Services\Quiz\Enums\ValueObjects\QuizStatusValue;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\DataTransferObjects\QuizItemDto;
use App\Services\Quiz\Managers\QuizItemStatusManager;
use App\ValueObjects\ScalarTypes\SimpleStringValue;

final class AdminQuizItemService
{
    public function __construct(
            private ModifiersInterface $modifiers,
            private AdminQuizItemQueriesInterface $adminQuizItemQueries,
            private AdminQuizQueriesInterface $adminQuizQueries,
    ) {
    }
    
    /**
     * Возвращает данные вопроса с ответами для карточки вопроса
     * 
     * @param int $id
     * @return QuizItem
     */
    public function getQuizItemByIdWithAnswers(int $id): QuizItem
    {
        $quizItem = $this->adminQuizItemQueries->getByIdWithAnswers($id);
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
        $quiz = $this->adminQuizQueries->getById($dto->quizId);
        QuizStatusValue::create($quiz->status)->allowQuizChanges();
        
        $quizItem = new QuizItem();
        $quizItem->quiz_id = $dto->quizId;
        $quizItem->description = $dto->description->value;
        
        $this->modifiers->save($quizItem);
        
        return $quizItem;
    }
    
    /**
     * Изменяет существующий вопрос в базе.
     * 
     * @param SimpleStringValue $description
     * @param int $id - id вопроса
     * @return QuizItem
     */
    public function update(SimpleStringValue $description, int $id): QuizItem
    {
        $quizItem = $this->adminQuizItemQueries->getById($id);
        $this->checkQuizItemEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $quizItem->description = $description->value;
        
        $this->modifiers->save($quizItem);
        
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
        $quizItem = $this->adminQuizItemQueries->getByIdWithAnswers($id);
        $this->checkQuizItemEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $manager = new QuizItemStatusManager($quizItem);
        $manager->defineNewStatus();
        
        if ($manager->checkOldAndNewStatusAreNotEqual()) {
            $quizItem->status = $manager->getNewStatusValue();
            $this->modifiers->save($quizItem);
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
        $quizItem = $this->adminQuizItemQueries->getByIdWithAnswers($id);
        $this->checkQuizItemEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $manager = new QuizItemStatusManager($quizItem);
        $manager->approveNewStatus($newStatus->status);
        
        $quizItem->status = $manager->getNewStatusValue();
        $this->modifiers->save($quizItem);
        
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
        $quizItem = $this->adminQuizItemQueries->getByIdWithAnswers($id);
        QuizItemStatusValue::create($quizItem->status)->checkFinalStatus();
        
        $manager = new QuizItemStatusManager($quizItem);
        $manager->defineNewStatus();
        
        $quizItem->status = $manager->getNewStatusValue();
        $this->modifiers->save($quizItem);
        
        return $quizItem;
    }
    
    /**
     * Проверяет возможность редактирования вопроса.
     * 
     * @param string $quizStatus
     * @param string $quizItemStatus
     * @return void
     */
    private function checkQuizItemEditabilityByStatuses(string $quizStatus, string $quizItemStatus): void
    {
        QuizStatusValue::create($quizStatus)->allowQuizChanges();
        QuizItemStatusValue::create($quizItemStatus)->allowQuizItemChanges();
    }
}

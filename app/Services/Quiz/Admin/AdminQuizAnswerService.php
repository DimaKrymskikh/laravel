<?php

namespace App\Services\Quiz\Admin;

use App\Models\Quiz\QuizAnswer;
use App\Modifiers\Quiz\QuizAnswerModifiersInterface;
use App\Queries\Quiz\QuizAnswers\QuizAnswerQueriesInterface;
use App\Queries\Quiz\QuizItems\QuizItemQueriesInterface;
use App\Services\Quiz\Enums\ValueObjects\QuizItemStatusValue;
use App\Services\Quiz\Enums\ValueObjects\QuizStatusValue;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\DataTransferObjects\QuizAnswerDto;
use App\Services\Quiz\Fields\QuizAnswerField;

final class AdminQuizAnswerService
{
    public function __construct(
            private QuizAnswerModifiersInterface $quizAnswerModifiers,
            private QuizAnswerQueriesInterface $quizAnswerQueries,
            private QuizItemQueriesInterface $quizItemQueries,
    ) {
    }
    
    /**
     * Возвращает данные для карточки ответа
     * 
     * @param int $id - id ответа
     * @return QuizAnswer
     */
    public function getAnswerCard(int $id): QuizAnswer
    {
        $quizAnswer =  $this->quizAnswerQueries->getById($id);
        $quizAnswer->quizItem->status = QuizItemStatus::from($quizAnswer->quizItem->status)->getInfo();
        $quizAnswer->quizItem->quiz->status = QuizStatus::from($quizAnswer->quizItem->quiz->status)->getInfo();
        
        return $quizAnswer;
    }
    
    /**
     * Записывает в таблицу 'quiz.quiz_answers' новый ответ
     * 
     * @param QuizAnswerDto $dto
     * @return QuizAnswer
     */
    public function create(QuizAnswerDto $dto): QuizAnswer
    {
        $quizItem = $this->quizItemQueries->getById($dto->quizItemId);
        $this->checkAnswerEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $quizAnswer = new QuizAnswer();
        $quizAnswer->quiz_item_id = $dto->quizItemId;
        $quizAnswer->description = $dto->description->value;
        $quizAnswer->is_correct = $dto->isCorrect->value;
        
        $this->quizAnswerModifiers->save($quizAnswer);
        
        return $quizAnswer;
    }
    
    /**
     * Изменяет одно поле ответа в таблице 'quiz.quiz_answers'.
     * Возвращает изменённый ответ
     * 
     * @param int $id - id ответа
     * @param QuizAnswerField $quizAnswerField
     * @return QuizAnswer
     */
    public function updateField(int $id, QuizAnswerField $quizAnswerField): QuizAnswer
    {
        $quizAnswer = $this->quizAnswerQueries->getById($id);
        $quizItem = $this->quizItemQueries->getById($quizAnswer->quiz_item_id);
        $this->checkAnswerEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $field = $quizAnswerField->field;
        $quizAnswer->$field = $quizAnswerField->value->value;
        
        $this->quizAnswerModifiers->save($quizAnswer);
        
        return $quizAnswer;
    }
    
    /**
     * Удаляет ответ в таблице 'quiz.quiz_answers'.
     * Возвращает id вопроса, которому принадлежал ответ
     * 
     * @param int $id - id ответа
     * @return int
     */
    public function delete(int $id): int
    {
        $quizAnswer = $this->quizAnswerQueries->getById($id);
        $quizItem = $this->quizItemQueries->getById($quizAnswer->quiz_item_id);
        $this->checkAnswerEditabilityByStatuses($quizItem->quiz->status, $quizItem->status);
        
        $this->quizAnswerModifiers->remove($quizAnswer);
        
        return $quizAnswer->quiz_item_id;
    }
    
    /**
     * По состоянию статусов проверяет, что ответ можно редактировать
     * 
     * @param string $quizStatus - статус опроса
     * @param string $quizItemStatus - статус вопроса
     * @return void
     */
    private function checkAnswerEditabilityByStatuses(string $quizStatus, string $quizItemStatus): void
    {
        QuizStatusValue::create($quizStatus)->allowQuizChanges();
        QuizItemStatusValue::create($quizItemStatus)->allowQuizItemChanges();
    }
}

<?php

namespace Database\Testsupport\Quiz;

/**
 * Содержит методы для посева данных опросов с вопросами и ответами.
 * Применяется в тестах
 */
trait QuizData
{
    /**
     * Посев только опросов
     * 
     * @return void
     */
    private function seedQuizzes(): void
    {
        $this->seed([
                \Database\Seeders\Tests\Quiz\QuizSeeder::class,
        ]);
    }
    
    /**
     * Посев опросов с вопросами
     * 
     * @return void
     */
    private function seedQuizzesWithQuizItems(): void
    {
        $this->seedQuizzes();
        $this->seed([
                \Database\Seeders\Tests\Quiz\QuizItemSeeder::class,
        ]);
    }
    
    /**
     * Посев опросов с вопросами и ответами
     * 
     * @return void
     */
    private function seedQuizzesWithQuizItemsAndAnswers(): void
    {
        $this->seedQuizzesWithQuizItems();
        $this->seed([
                \Database\Seeders\Tests\Quiz\QuizAnswerSeeder::class,
        ]);
    }
}

<?php

namespace Database\Testsupport\Quiz;

/**
 * Содержит методы для посева данных опросов с ответами, которые проходят или прошли пользователи.
 * Применяется в тестах
 */
trait TrialData
{
    /**
     * Посев только опросов для проверки знаний пользователей
     * 
     * @return void
     */
    private function seedTrials(): void
    {
        $this->seed([
                \Database\Seeders\Tests\Quiz\TrialSeeder::class,
        ]);
    }
    
    /**
     * Посев опросов с ответами, которые дали пользователи
     * 
     * @return void
     */
    private function seedTrialsAndTrialAnswers(): void
    {
        $this->seedTrials();
        $this->seed([
                \Database\Seeders\Tests\Quiz\TrialAnswerSeeder::class,
        ]);
    }
}

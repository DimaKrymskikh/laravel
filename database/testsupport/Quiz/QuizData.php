<?php

namespace Database\Testsupport\Quiz;

trait QuizData
{
    private function seedQuizzes(): void
    {
        $this->seed([
                \Database\Seeders\Tests\Quiz\QuizSeeder::class,
        ]);
    }
    
    private function seedQuizzesWithQuizItems(): void
    {
        $this->seed([
                \Database\Seeders\Tests\Quiz\QuizSeeder::class,
                \Database\Seeders\Tests\Quiz\QuizItemSeeder::class,
        ]);
    }
    
    private function seedQuizzesWithQuizItemsAndAnswers(): void
    {
        $this->seed([
                \Database\Seeders\Tests\Quiz\QuizSeeder::class,
                \Database\Seeders\Tests\Quiz\QuizItemSeeder::class,
                \Database\Seeders\Tests\Quiz\QuizAnswerSeeder::class,
        ]);
    }
}

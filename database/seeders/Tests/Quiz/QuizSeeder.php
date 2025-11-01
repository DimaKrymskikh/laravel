<?php

namespace Database\Seeders\Tests\Quiz;

use App\Services\Quiz\Enums\QuizStatus;
use App\Support\Database\Sequences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    use WithoutModelEvents, Sequences;
    
    public const ID_ARITHMETIC_OPERATIONS  = 1;
    public const ID_STRAIGHT_LINES_ON_THE_PLANE = 2;
    public const ID_TRIANGLES = 3;
    public const ID_REMOVED_STATUS = 4;

    public function run(): void
    {
        $tableName = 'quiz.quizzes';
        
        foreach ($this->getQuizzes() as $quiz) {
            DB::table($tableName)->insert([
                'id' => $quiz->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'status' => $quiz->status ?? 'at_work',
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    public function getQuizzes(): array
    {
        return [
            (object) [
                'id' => self::ID_ARITHMETIC_OPERATIONS,
                'title' => 'Арифметические операции (тест)',
                'description' => 'Изучаются арифметические операции.'
            ],
            (object) [
                'id' => self::ID_STRAIGHT_LINES_ON_THE_PLANE,
                'title' => 'Прямые на плоскости (тест)',
                'description' => null
            ],
            (object) [
                'id' => self::ID_TRIANGLES,
                'title' => 'Треугольники (тест)',
                'description' => 'Изучаются треугольники.',
                'status' => QuizStatus::Approved,
            ],
            (object) [
                'id' => self::ID_REMOVED_STATUS,
                'title' => 'Удалённый',
                'description' => 'Этот опрос удалён.',
                'status' => QuizStatus::Removed,
            ],
        ];
    }
}

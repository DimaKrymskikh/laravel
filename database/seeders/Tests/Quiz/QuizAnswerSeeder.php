<?php

namespace Database\Seeders\Tests\Quiz;

use App\Support\Database\Sequences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizAnswerSeeder extends Seeder
{
    use WithoutModelEvents, Sequences;
    
    const ID_SUM_OF_NUMBERS_IS_3 = 1;
    const ID_SUM_OF_NUMBERS_IS_4 = 2;
    const ID_SUM_OF_NUMBERS_IS_5 = 3;

    public function run(): void
    {
        $tableName = 'quiz.quiz_answers';
        
        foreach ($this->getQuizAnswers() as $answer) {
            DB::table($tableName)->insert([
                'id' => $answer->id,
                'description' => $answer->description,
                'quiz_item_id' => $answer->quizItemId
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    public function getQuizAnswers(): array
    {
        return [
            (object) [
                'id' => self::ID_SUM_OF_NUMBERS_IS_3,
                'description' => 'три',
                'is_correct' => false,
                'quizItemId' => QuizItemSeeder::ID_SUM_OF_NUMBERS,
            ],
            (object) [
                'id' => self::ID_SUM_OF_NUMBERS_IS_4,
                'description' => 'четыре',
                'is_correct' => true,
                'quizItemId' => QuizItemSeeder::ID_SUM_OF_NUMBERS,
            ],
            (object) [
                'id' => self::ID_SUM_OF_NUMBERS_IS_5,
                'description' => 'пять',
                'is_correct' => false,
                'quizItemId' => QuizItemSeeder::ID_SUM_OF_NUMBERS,
            ],
        ];
    }
}

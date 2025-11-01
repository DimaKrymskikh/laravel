<?php

namespace Database\Seeders\Tests\Quiz;

use App\Services\Quiz\Enums\QuizItemStatus;
use App\Support\Database\Sequences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizItemSeeder extends Seeder
{
    use WithoutModelEvents, Sequences;

    const ID_SUM_OF_NUMBERS = 1;
    const ID_DIFFERENCE_OF_NUMBERS = 2;
    const ID_REMOVED_STATUS = 3;
        
    public function run(): void
    {
        $tableName = 'quiz.quiz_items';
        
        foreach ($this->getQuizItems() as $quizItem) {
            $data = [
                'id' => $quizItem->id,
                'description' => $quizItem->description,
                'quiz_id' => $quizItem->quizId
            ];
            
            if(isset($quizItem->status)) {
                $data['status'] = $quizItem->status;
            }
            
            DB::table($tableName)->insert($data);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    public function getQuizItems(): array
    {
        return [
            (object) [
                'id' => self::ID_SUM_OF_NUMBERS,
                'description' => 'Чему равно 2 + 9 ?',
                'quizId' => QuizSeeder::ID_ARITHMETIC_OPERATIONS,
            ],
            (object) [
                'id' => self::ID_DIFFERENCE_OF_NUMBERS,
                'description' => 'Чему равно 5 - 7 ?',
                'quizId' => QuizSeeder::ID_ARITHMETIC_OPERATIONS,
            ],
            (object) [
                'id' => self::ID_REMOVED_STATUS,
                'description' => 'Этот вопрос удалён',
                'quizId' => QuizSeeder::ID_ARITHMETIC_OPERATIONS,
                'status' => QuizItemStatus::Removed->value,
            ]
        ];
    }
}

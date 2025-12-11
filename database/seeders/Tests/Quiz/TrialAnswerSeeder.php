<?php

namespace Database\Seeders\Tests\Quiz;

use App\Support\Database\Sequences;
use Database\Seeders\Tests\Quiz\QuizItemSeeder;
use Database\Seeders\Tests\Quiz\TrialSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Посев таблицы 'quiz.trial_answers'
 */
class TrialAnswerSeeder extends Seeder
{
    use WithoutModelEvents, Sequences;

    public const ID_FOR_SUM_OF_NUMBERS = 1;
    
    public function run(): void
    {
        $tableName = 'quiz.trial_answers';
        
        foreach ($this->getTrialAnswers() as $answer) {
            DB::table($tableName)->insert([
                'id' => $answer->id,
                'trial_id' => $answer->trial_id,
                'quiz_item_id' => $answer->quiz_item_id,
                'question' => $answer->question,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    public function getTrialAnswers(): array
    {
        return [
            (object) [
                'id' => self::ID_FOR_SUM_OF_NUMBERS,
                'trial_id' => TrialSeeder::ID_ACTIVE_TRIAL_BY_AUTH,
                'quiz_item_id' => QuizItemSeeder::ID_SUM_OF_NUMBERS,
                'question' => 'Чему равно 2 + 9 ?',
            ],
        ];
    }
}

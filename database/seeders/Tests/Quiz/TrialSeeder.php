<?php

namespace Database\Seeders\Tests\Quiz;

use App\Support\Database\Sequences;
use Database\Seeders\Tests\Person\UserSeeder;
use Database\Seeders\Tests\Quiz\QuizSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Посев таблицы 'quiz.trials'
 */
class TrialSeeder extends Seeder
{
    use WithoutModelEvents, Sequences;
    
    public const ID_COMPLETED_TRIAL_BY_AUTH = 1;
    public const ID_ACTIVE_TRIAL_BY_AUTH = 2;
    
    public function run(): void
    {
        $tableName = 'quiz.trials';
        
        foreach ($this->getTrials() as $trial) {
            DB::table($tableName)->insert([
                'id' => $trial->id,
                'user_id' => $trial->user_id,
                'quiz_id' => $trial->quiz_id,
                'end_at' => $trial->end_at ?? null,
                'title' => $trial->title,
                'lead_time' => $trial->lead_time,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    public function getTrials(): array
    {
        return [
            (object) [
                'id' => self::ID_COMPLETED_TRIAL_BY_AUTH,
                'user_id' => UserSeeder::ID_AUTH_TEST_LOGIN,
                'quiz_id' => QuizSeeder::ID_APPROVED_STATUS,
                'end_at' => now(),
                'title' => 'Утверждённый',
                'lead_time' => 10
            ],
            (object) [
                'id' => self::ID_ACTIVE_TRIAL_BY_AUTH,
                'user_id' => UserSeeder::ID_AUTH_TEST_LOGIN,
                'quiz_id' => QuizSeeder::ID_APPROVED_STATUS,
                'title' => 'Утверждённый',
                'lead_time' => 10
            ],
        ];
    }
}

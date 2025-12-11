<?php

namespace Tests\Unit\Services\Quiz\Managers;

use App\Models\Quiz\Trial;
use App\Services\Quiz\Enums\Grade;
use App\Services\Quiz\Managers\GradeManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GradeManagerTest extends TestCase
{
    public static function gradeProvider(): array
    {
        return [
            [
                Grade::Excellent, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 20,
                ])->make()
            ],
            [
                Grade::Excellent, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 19,
                ])->make()
            ],
            [
                Grade::Excellent, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 18,
                ])->make()
            ],
            [
                Grade::Good, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 17,
                ])->make()
            ],
            [
                Grade::Good, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 16,
                ])->make()
            ],
            [
                Grade::Satisfactory, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 15,
                ])->make()
            ],
            [
                Grade::Satisfactory, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 13,
                ])->make()
            ],
            [
                Grade::Satisfactory, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 12,
                ])->make()
            ],
            [
                Grade::Fail, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 11,
                ])->make()
            ],
            [
                Grade::Fail, Trial::factory()->state([
                    'total_quiz_items' => 20,
                    'correct_answers_number' => 0,
                ])->make()
            ],
        ];
    }
    
    #[DataProvider('gradeProvider')]
    public function test_success_find(Grade $grade, Trial $trial): void
    {
        $this->assertEquals($grade->value, GradeManager::find($trial));
    }
}

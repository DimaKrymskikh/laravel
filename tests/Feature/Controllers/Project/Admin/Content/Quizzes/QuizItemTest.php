<?php

namespace Tests\Feature\Controllers\Project\Admin\Content\Quizzes;

use App\Services\Quiz\Enums\QuizItemStatus;
use Database\Seeders\Tests\Quiz\QuizItemSeeder;
use Database\Seeders\Tests\Quiz\QuizSeeder;
use Database\Testsupport\Quiz\QuizData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Database\Testsupport\Authentication;
use Tests\TestCase;

final class QuizItemTest extends TestCase
{
    use RefreshDatabase, Authentication, QuizData;
    
    public function test_success_store(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzes();
        
        $textQuizItem = 'Текст вопроса';
        
        $response = $this->actingAs($user)->post('admin/quiz_items', [
            'quiz_id' => QuizSeeder::ID_STRAIGHT_LINES_ON_THE_PLANE,
            'description' => $textQuizItem,
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quizzes.show', [
            'quiz' => QuizSeeder::ID_STRAIGHT_LINES_ON_THE_PLANE
        ]);

        $this->assertDatabaseHas('quiz.quiz_items', [
            'description' => $textQuizItem,
        ]);
    }
    
    public function test_success_show(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzesWithQuizItemsAndAnswers();
        
        $response = $this->actingAs($user)->get('admin/quiz_items/'.QuizItemSeeder::ID_SUM_OF_NUMBERS);
        
        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Quizzes/QuizItemCard')
                        ->etc()
                );
    }
    
    public function test_success_update(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzesWithQuizItems();
        
        $textQuizItem = 'Новый текст вопроса';
        
        $response = $this->actingAs($user)->put('admin/quiz_items/'.QuizItemSeeder::ID_SUM_OF_NUMBERS, [
            'field' => 'description',
            'value' => $textQuizItem,
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quizzes.show', [
            'quiz' => QuizSeeder::ID_ARITHMETIC_OPERATIONS
        ]);

        $this->assertDatabaseHas('quiz.quiz_items', [
            'id' => QuizItemSeeder::ID_SUM_OF_NUMBERS,
            'description' => $textQuizItem,
        ]);
    }
    
    public function test_success_setFinalStatus(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzesWithQuizItems();
        
        $quizItemId = QuizItemSeeder::ID_SUM_OF_NUMBERS;
        $status = QuizItemStatus::Removed->value;
        
        $response = $this->actingAs($user)->put("admin/quiz_items/$quizItemId/set_status", [
            'status' => $status,
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quizzes.show', [
            'quiz' => QuizSeeder::ID_ARITHMETIC_OPERATIONS
        ]);

        $this->assertDatabaseHas('quiz.quiz_items', [
            'id' => $quizItemId,
            'status' => $status,
        ]);
    }
    
    public function test_success_cancelFinalStatus(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzesWithQuizItems();
        
        $quizItemId = QuizItemSeeder::ID_REMOVED_STATUS;
        // В начальный момент статус 'удалён'
        $this->assertDatabaseHas('quiz.quiz_items', [
            'id' => $quizItemId,
            'status' => QuizItemStatus::Removed->value,
        ]);
        
        $response = $this->actingAs($user)->put("admin/quiz_items/$quizItemId/cancel_status");
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quizzes.show', [
            'quiz' => QuizSeeder::ID_ARITHMETIC_OPERATIONS
        ]);

        // Статус изменился
        $this->assertDatabaseMissing('quiz.quiz_items', [
            'id' => $quizItemId,
            'status' => QuizItemStatus::Removed->value,
        ]);
    }
}

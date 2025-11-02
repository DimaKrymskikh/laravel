<?php

namespace Tests\Feature\Controllers\Project\Admin\Content\Quizzes;

use App\Services\Quiz\Enums\QuizStatus;
use Database\Seeders\Tests\Quiz\QuizSeeder;
use Database\Testsupport\Quiz\QuizData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Database\Testsupport\Authentication;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase, Authentication, QuizData;
    
    public function test_success_index(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $response = $this->actingAs($user)->get('admin/quizzes');

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Quizzes/Quizzes')
                        ->etc()
                );
    }
    
    public function test_fail_index_because_the_user_is_not_an_admin(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AuthTestLogin');
        
        $response = $this->actingAs($user)->get('admin/quizzes');

        $response->assertStatus(403);
    }
    
    public function test_success_store(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $title = 'Название опроса';
        
        $response = $this->actingAs($user)->post('admin/quizzes', [
            'title' => $title,
            'description' => 'Описание опроса',
            'lead_time' => '15',
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quizzes.index');

        $this->assertDatabaseHas('quiz.quizzes', [
            'title' => $title,
        ]);
    }
    
    public function test_success_show(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzes();
        
        $response = $this->actingAs($user)->get('admin/quizzes/'.QuizSeeder::ID_STRAIGHT_LINES_ON_THE_PLANE);

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Quizzes/QuizCard')
                        ->etc()
                );
    }
    
    public function test_success_update(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzes();
        
        $newTitle = 'Название опроса';
        
        $response = $this->actingAs($user)->put('admin/quizzes/'.QuizSeeder::ID_ARITHMETIC_OPERATIONS, [
            'field' => 'title',
            'value' => $newTitle,
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quizzes.index');

        $this->assertDatabaseHas('quiz.quizzes', [
            'id' => QuizSeeder::ID_ARITHMETIC_OPERATIONS,
            'title' => $newTitle,
        ]);
    }
    
    public function test_success_setFinalStatus(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzes();
        
        $quizId = QuizSeeder::ID_ARITHMETIC_OPERATIONS;
        
        $response = $this->actingAs($user)->put("admin/quizzes/$quizId/set_status", [
            'status' => QuizStatus::Removed->value,
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quizzes.index');

        $this->assertDatabaseHas('quiz.quizzes', [
            'id' => $quizId,
            'status' => QuizStatus::Removed->value,
        ]);
    }
    
    public function test_success_cancelFinalStatus(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzes();
        
        $quizId = QuizSeeder::ID_REMOVED_STATUS;
        $this->assertDatabaseHas('quiz.quizzes', [
            'id' => $quizId,
            'status' => QuizStatus::Removed->value,
        ]);
        
        $response = $this->actingAs($user)->put("admin/quizzes/$quizId/cancel_status");
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quizzes.index');

        $this->assertDatabaseMissing('quiz.quizzes', [
            'id' => $quizId,
            'status' => QuizStatus::Removed->value,
        ]);
    }
}

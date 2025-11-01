<?php

namespace Tests\Feature\Controllers\Project\Admin\Content\Quizzes;

use Database\Seeders\Tests\Quiz\QuizAnswerSeeder;
use Database\Seeders\Tests\Quiz\QuizItemSeeder;
use Database\Testsupport\Quiz\QuizData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Support\Authentication;
use Tests\Support\Seeders;
use Tests\TestCase;

class QuizAnswerTest extends TestCase
{
    use RefreshDatabase, Authentication, Seeders, QuizData;
    
    public function test_success_store(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzesWithQuizItemsAndAnswers();
        
        $textQuizAnswer = 'Текст ответа';
        
        $response = $this->actingAs($user)->post('admin/quiz_answers', [
            'quiz_item_id' => QuizItemSeeder::ID_DIFFERENCE_OF_NUMBERS,
            'description' => $textQuizAnswer,
            'is_correct' => true
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quiz_items.show', [
            'quiz_item' => QuizItemSeeder::ID_DIFFERENCE_OF_NUMBERS
        ]);

        $this->assertDatabaseHas('quiz.quiz_answers', [
            'description' => $textQuizAnswer,
            'is_correct' => true
        ]);
    }
    
    public function test_success_show(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzesWithQuizItemsAndAnswers();
        
        $response = $this->actingAs($user)->get('admin/quiz_answers/'.QuizAnswerSeeder::ID_SUM_OF_NUMBERS_IS_3);
        
        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Quizzes/QuizAnswerCard')
                        ->etc()
                );
    }
    
    public function test_success_update(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzesWithQuizItemsAndAnswers();
        
        $response = $this->actingAs($user)->put('admin/quiz_answers/'.QuizAnswerSeeder::ID_SUM_OF_NUMBERS_IS_3, [
            'quiz_item_id' => QuizItemSeeder::ID_SUM_OF_NUMBERS,
            'field' => 'is_correct',
            'value' => true
        ]);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quiz_items.show', [
            'quiz_item' => QuizItemSeeder::ID_SUM_OF_NUMBERS
        ]);

        $this->assertDatabaseHas('quiz.quiz_answers', [
            'id' => QuizAnswerSeeder::ID_SUM_OF_NUMBERS_IS_3,
            'is_correct' => true
        ]);
    }
    
    public function test_success_delete(): void
    {
        $this->seedUsers();
        $user = $this->getUser('AdminTestLogin');
        
        $this->seedQuizzesWithQuizItemsAndAnswers();
        
        $response = $this->actingAs($user)->delete('admin/quiz_answers/'.QuizAnswerSeeder::ID_SUM_OF_NUMBERS_IS_3);
        
        $response->assertStatus(302);
        $response->assertRedirectToRoute('admin.quiz_items.show', [
            'quiz_item' => QuizItemSeeder::ID_SUM_OF_NUMBERS
        ]);

        $this->assertDatabaseMissing('quiz.quiz_answers', [
            'id' => QuizAnswerSeeder::ID_SUM_OF_NUMBERS_IS_3,
        ]);
    }
}

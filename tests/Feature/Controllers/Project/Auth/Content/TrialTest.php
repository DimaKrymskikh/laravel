<?php

namespace Tests\Feature\Controllers\Project\Auth\Content;

use Database\Seeders\Tests\Quiz\QuizAnswerSeeder;
use Database\Seeders\Tests\Quiz\QuizSeeder;
use Database\Seeders\Tests\Quiz\TrialAnswerSeeder;
use Database\Testsupport\Authentication;
use Database\Testsupport\Quiz\QuizData;
use Database\Testsupport\Quiz\TrialData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TrialTest extends TestCase
{
    use RefreshDatabase, Authentication, QuizData, TrialData;
        
    public function test_success_index(): void
    {
        $this->seedUsers();
        $this->seedQuizzes();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->get('trials');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Trials/Trials')
            );
    }
        
    public function test_success_show(): void
    {
        $this->seedUsers();
        $this->seedQuizzes();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->get('trials/'.QuizSeeder::ID_APPROVED_STATUS);

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Trials/TrialStart')
            );
    }
        
    public function test_success_start(): void
    {
        $this->seedUsers();
        $this->seedQuizzesWithQuizItemsAndAnswers();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->post('trials/start', [
                'quiz_id' => QuizSeeder::ID_APPROVED_STATUS
            ]);

        $response->assertStatus(302);
        $response->assertRedirectToRoute('trials.trial');
    }
        
    public function test_success_showTrial(): void
    {
        $this->seedUsers();
        $this->seedQuizzesWithQuizItemsAndAnswers();
        $this->seedTrials();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->get('trials/show_trial');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Trials/TrialPage')
            );
    }
        
    public function test_success_getResults(): void
    {
        $this->seedUsers();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->get('trials/get_results');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => 
                    $page->component('Auth/Account/TrialResults')
            );
    }
        
    public function test_success_chooseAnswer(): void
    {
        $this->seedUsers();
        $this->seedQuizzesWithQuizItemsAndAnswers();
        $this->seedTrialsAndTrialAnswers();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->post('trials/choose_answer', [
                'id' => TrialAnswerSeeder::ID_FOR_SUM_OF_NUMBERS,
                'quiz_answer_id' => QuizAnswerSeeder::ID_SUM_OF_NUMBERS_IS_3
            ]);

        $response->assertStatus(302);
        $response->assertRedirectToRoute('trials.trial');

        $this->assertDatabaseHas('quiz.trial_answers', [
            'quiz_answer_id' => QuizAnswerSeeder::ID_SUM_OF_NUMBERS_IS_3,
        ]);
    }
        
    public function test_success_complete(): void
    {
        $this->seedUsers();
        $this->seedQuizzesWithQuizItemsAndAnswers();
        $this->seedTrialsAndTrialAnswers();
        
        $user = $this->getUser('AuthTestLogin');
        $acting = $this->actingAs($user);
        
        $response = $acting->post('trials/complete');

        $response->assertStatus(302);
        $response->assertRedirectToRoute('trials.results');
    }
}

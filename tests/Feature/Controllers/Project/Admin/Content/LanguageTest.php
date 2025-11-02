<?php

namespace Tests\Feature\Controllers\Project\Admin\Content;

use App\Models\Thesaurus\Language;
use App\Providers\RouteServiceProvider;
use Database\Seeders\Tests\Thesaurus\LanguageSeeder;
use Database\Testsupport\Thesaurus\ThesaurusData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia as Assert;
use Database\Testsupport\Authentication;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase, Authentication, ThesaurusData;
    
    public function test_languages_page_displayed_for_admin_without_languages(): void
    {
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_LANGUAGES);

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Languages')
                        ->has('languages', 0)
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_languages_page_displayed_for_admin_with_languages(): void
    {
        $this->seedLanguages();
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_LANGUAGES);

        $response
                ->assertStatus(200)
                ->assertInertia(fn (Assert $page) => 
                    $page->component('Admin/Languages')
                        ->has('languages', 2)
                        ->has('errors', 0)
                        ->etc()
                );
    }
    
    public function test_languages_page_can_not_displayed_for_auth_not_admin(): void
    {
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AuthTestLogin'));
        $response = $acting->get(RouteServiceProvider::URL_ADMIN_LANGUAGES);

        $response->assertStatus(403);
    }
    
    public function test_admin_can_add_language(): void
    {
        $this->seedLanguages();
        $nLanguages = Language::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_LANGUAGES, [
            'name' => 'Немецкий',
        ]);

        // Добавлен новый язык в таблицу 'thesaurus.languages'
        $this->assertEquals($nLanguages + 1, Language::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_LANGUAGES);
    }
    
    public function test_admin_can_not_add_language_if_the_name_starts_with_a_small_letter(): void
    {
        $this->seedLanguages();
        $nLanguages = Language::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_LANGUAGES, [
            'name' => 'немецкий',
        ]);

        // Число языков в таблице 'thesaurus.languages' не изменилось
        $this->assertEquals($nLanguages, Language::all()->count());

        $response
            ->assertInvalid([
                'name' => trans('attr.language.capital_first_letter')
            ]);
    }
    
    public function test_admin_can_not_add_language_if_the_language_name_already_exists(): void
    {
        $this->seedLanguages();
        $nLanguages = Language::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_LANGUAGES, [
            'name' => 'Русский',
        ]);

        // Число языков в таблице 'thesaurus.languages' не изменилось
        $this->assertEquals($nLanguages, Language::all()->count());

        $response
            ->assertInvalid([
                'name' => trans('attr.language.unique')
            ]);
    }
    
    public function test_admin_can_not_add_language_if_the_language_name_is_empty(): void
    {
        $this->seedLanguages();
        $nLanguages = Language::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_LANGUAGES, [
            'name' => '',
        ]);

        // Число языков в таблице 'thesaurus.languages' не изменилось
        $this->assertEquals($nLanguages, Language::all()->count());

        $response
            ->assertInvalid([
                'name' => trans('attr.language.required')
            ]);
    }
    
    public function test_admin_can_not_add_language_if_the_language_name_is_not_a_string(): void
    {
        $this->seedLanguages();
        $nLanguages = Language::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->post(RouteServiceProvider::URL_ADMIN_LANGUAGES, [
            'name' => 77,
        ]);

        // Число языков в таблице 'thesaurus.languages' не изменилось
        $this->assertEquals($nLanguages, Language::all()->count());

        $response
            ->assertInvalid([
                'name' => trans('attr.language.string')
            ]);
    }
    
    public function test_admin_can_update_language(): void
    {
        $this->seedLanguages();
        $nLanguages = Language::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->put(RouteServiceProvider::URL_ADMIN_LANGUAGES.'/'.LanguageSeeder::ID_ENGLISH, [
            'name' => 'Китайский',
        ]);

        // Число записей в таблице 'thesaurus.languages' не изменилось
        $this->assertEquals($nLanguages, Language::all()->count());
        // Изменилось название языка
        $this->assertEquals('Китайский', Language::find(LanguageSeeder::ID_ENGLISH)->name);

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_LANGUAGES);
    }
    
    public function test_admin_can_delete_language(): void
    {
        $this->seedLanguages();
        $nLanguages = Language::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->delete(RouteServiceProvider::URL_ADMIN_LANGUAGES.'/'.LanguageSeeder::ID_ENGLISH, [
            'password' => 'AdminTestPassword1',
        ]);

        // Число записей в таблице 'thesaurus.languages' уменьшилось на 1
        $this->assertEquals($nLanguages - 1, Language::all()->count());

        $response
            ->assertStatus(302)
            ->assertRedirect(RouteServiceProvider::URL_ADMIN_LANGUAGES);
    }
    
    public function test_admin_can_not_delete_language_if_the_password_is_incorrect(): void
    {
        $this->seedLanguages();
        $nLanguages = Language::all()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->delete(RouteServiceProvider::URL_ADMIN_LANGUAGES.'/'.LanguageSeeder::ID_ENGLISH, [
            'password' => 'IncorrectPassword13',
        ]);

        // Число записей в таблице 'thesaurus.languages' не изменилось
        $this->assertEquals($nLanguages, Language::all()->count());

        $response
            ->assertInvalid([
                'password' => trans("user.password.wrong")
            ]);
    }
    
    public function test_admin_can_get_language_list_in_json_format(): void
    {
        $this->seedLanguages();
        $name = 'ру';
        $nLanguages = Language::where('name', 'ILIKE', "%$name%")->get()->count();
        
        $this->seedUsers();
        $acting = $this->actingAs($this->getUser('AdminTestLogin'));
        $response = $acting->getJson("/languages/getJson?name_filter=$name");

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has($nLanguages)
            );
    }
}

<?php

namespace Tests\Feature\Commands\Dvd\Actor;

use App\Models\Dvd\Actor;
use Database\Testsupport\Dvd\DvdData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateActorTest extends TestCase
{
    use RefreshDatabase, DvdData;
    
    public function test_actor_can_be_create(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this
            ->artisan('create:actor')
            ->expectsQuestion('Введите имя актёра?', 'Андрей')
            ->expectsQuestion('Введите фамилию актёра?', 'Миронов')
            ->expectsOutput('В таблицу dvd.actors добавлен актёр Андрей Миронов.')
            ->expectsOutput('Команда выполнена.')
            ->assertExitCode(0);

        // Добавлен новый актёр в таблицу 'dvd.actors'
        $this->assertEquals($nActors + 1, Actor::all()->count());
    }
    
    public function test_actor_can_not_be_create_if_first_name_or_the_last_name_empty(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this
            ->artisan('create:actor')
            ->expectsQuestion('Введите имя актёра?', '')
            ->expectsQuestion('Введите фамилию актёра?', '')
            ->expectsOutput('Имя актёра должно начинаться с заглавной буквы. Остальные буквы должны быть строчными.')
            ->assertExitCode(0);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());
        
        $this
            ->artisan('create:actor')
            ->expectsQuestion('Введите имя актёра?', 'Андрей')
            ->expectsQuestion('Введите фамилию актёра?', null)
            ->expectsOutput('Фамилия актёра должна начинаться с заглавной буквы. Остальные буквы должны быть строчными.')
            ->assertExitCode(0);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());
    }
    
    public function test_actor_can_not_be_create_if_the_first_name_or_the_last_name_starts_with_a_small_letter(): void
    {
        $this->seedActors();
        $nActors = Actor::all()->count();
        
        $this
            ->artisan('create:actor')
            ->expectsQuestion('Введите имя актёра?', 'андрей')
            ->expectsQuestion('Введите фамилию актёра?', 'Миронов')
            ->expectsOutput(trans('attr.actor.first_name.capital_first_letter'))
            ->assertExitCode(0);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());
        
        $this
            ->artisan('create:actor')
            ->expectsQuestion('Введите имя актёра?', 'Андрей')
            ->expectsQuestion('Введите фамилию актёра?', 'миронов')
            ->expectsOutput(trans('attr.actor.last_name.capital_first_letter'))
            ->assertExitCode(0);

        // В таблице 'dvd.actors' число актёров не изменилось
        $this->assertEquals($nActors, Actor::all()->count());
    }
}

<?php

namespace Tests\Feature\Events;

use App\Events\AddFilmInUserList;
use App\Models\Dvd\Film;
use Database\Seeders\Tests\Dvd\FilmSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\Authentication;
use Tests\Support\User\Seeders;
use Tests\TestCase;

class AddFilmInUserListTest extends TestCase
{
    use RefreshDatabase, Authentication, Seeders;
    
    public function test_film_can_be_add_in_user_list(): void
    {
        $this->seedUserFilms();
        
        $user = $this->getUser('AuthTestLogin');
        
        $filmTitle = Film::find(FilmSeeder::ID_BOILED_DARES)->title;
        
        $addCityInWeatherList = new AddFilmInUserList($user->id, FilmSeeder::ID_BOILED_DARES);
        
        $broadcastWith = $addCityInWeatherList->broadcastWith();
        // Проверка отправляемого сообщения
        $this->assertEquals($broadcastWith['message'], trans('event_messages.add_film_in_user_list', ['filmTitle' => $filmTitle]));
        
        // Проверка имени канала
        $channelName = $addCityInWeatherList->broadcastOn()[0]->name;
        $this->assertEquals($channelName, "private-auth.$user->id");
    }
}

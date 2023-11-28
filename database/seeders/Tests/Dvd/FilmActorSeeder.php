<?php

namespace Database\Seeders\Tests\Dvd;

use Database\Seeders\Tests\Dvd\ActorSeeder;
use Database\Seeders\Tests\Dvd\FilmSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmActorSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getFilmsActors() as $item) {
            DB::table('dvd.films_actors')->insert([
                'film_id' => $item->film_id,
                'actor_id' => $item->actor_id,
            ]);
        }
    }
    
    private function getFilmsActors(): array
    {
        return [
            (object) [
                'film_id' => FilmSeeder::ID_ADAPTATION_HOLES,
                'actor_id' => ActorSeeder::ID_ED_CHASE,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_ADAPTATION_HOLES,
                'actor_id' => ActorSeeder::ID_JENNIFER_DAVIS,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_ADAPTATION_HOLES,
                'actor_id' => ActorSeeder::ID_JOHNNY_LOLLOBRIGIDA,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_BOILED_DARES,
                'actor_id' => ActorSeeder::ID_NICK_WAHLBERG,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_BOILED_DARES,
                'actor_id' => ActorSeeder::ID_ED_CHASE,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_BOOGIE_AMELIE,
                'actor_id' => ActorSeeder::ID_ED_CHASE,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_BOOGIE_AMELIE,
                'actor_id' => ActorSeeder::ID_JENNIFER_DAVIS,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_BOOGIE_AMELIE,
                'actor_id' => ActorSeeder::ID_JOHNNY_LOLLOBRIGIDA,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_BOOGIE_AMELIE,
                'actor_id' => ActorSeeder::ID_NICK_WAHLBERG,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_BOOGIE_AMELIE,
                'actor_id' => ActorSeeder::ID_PENELOPE_GUINESS,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_JAPANESE_RUN,
                'actor_id' => ActorSeeder::ID_JOHNNY_LOLLOBRIGIDA,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_JAPANESE_RUN,
                'actor_id' => ActorSeeder::ID_NICK_WAHLBERG,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_KISS_GLORY,
                'actor_id' => ActorSeeder::ID_JENNIFER_DAVIS,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_KISS_GLORY,
                'actor_id' => ActorSeeder::ID_NICK_WAHLBERG,
            ],
            (object) [
                'film_id' => FilmSeeder::ID_KISS_GLORY,
                'actor_id' => ActorSeeder::ID_PENELOPE_GUINESS,
            ],
        ];
    }
}

<?php

namespace Database\Seeders\Dvd;

use Database\Copy\Dvd\FilmActorData;
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
    
    private function getFilmsActors(): iterable
    {
        yield from (new FilmActorData())();
    }
}

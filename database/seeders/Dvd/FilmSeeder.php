<?php

namespace Database\Seeders\Dvd;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Database\Copy\Dvd\FilmData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'dvd.films';
        
        foreach ($this->getFilms() as $film) {
            DB::table($tableName)->insert([
                'id' => $film->id,
                'title' => $film->title,
                'description' => $film->description,
                'release_year' => $film->release_year,
                'language_id' => $film->language_id,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    private function getFilms(): iterable
    {
        yield from (new FilmData())();
    }
}

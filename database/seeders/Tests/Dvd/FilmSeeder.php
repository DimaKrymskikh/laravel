<?php

namespace Database\Seeders\Tests\Dvd;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Database\Seeders\Tests\Thesaurus\LanguageSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FilmSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    const ID_ADAPTATION_HOLES = 1;
    const ID_BOILED_DARES = 2;
    const ID_BOOGIE_AMELIE = 3;
    const ID_JAPANESE_RUN = 4;
    const ID_KISS_GLORY = 5;
    const ID_NOTTING_SPEAKEASY = 6;
    const ID_NOVOCAINE_FLIGHT = 7;
    const ID_PUNK_DIVORCE = 8;
    const ID_RIVER_OUTLAW = 9;
    const ID_SAINTS_BRIDE = 10;
    const ID_SHINING_ROSES = 11;
    const ID_TIGHTS_DAWN = 12;
    
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
    
    private function getFilms(): array
    {
        return [
            (object) [
                'id' => self::ID_ADAPTATION_HOLES,
                'title' => 'Adaptation Holes',
                'description' => 'A Astounding Reflection of a Lumberjack And a Car who must Sink a Lumberjack in A Baloon Factory',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_BOILED_DARES,
                'title' => 'Boiled Dares',
                'description' => 'A Awe-Inspiring Story of a Waitress And a Dog who must Discover a Dentist in Ancient Japan',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_BOOGIE_AMELIE,
                'title' => 'Boogie Amelie',
                'description' => 'A Lacklusture Character Study of a Husband And a Sumo Wrestler who must Succumb a Technical Writer in The Gulf of Mexico',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_JAPANESE_RUN,
                'title' => 'Japanese Run',
                'description' => 'A Awe-Inspiring Epistle of a Feminist And a Girl who must Sink a Girl in The Outback',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_KISS_GLORY,
                'title' => 'Kiss Glory',
                'description' => 'A Lacklusture Reflection of a Girl And a Husband who must Find a Robot in The Canadian Rockies',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_NOTTING_SPEAKEASY,
                'title' => 'Notting Speakeasy',
                'description' => 'A Thoughtful Display of a Butler And a Womanizer who must Find a Waitress in The Canadian Rockies',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_NOVOCAINE_FLIGHT,
                'title' => 'Novocaine Flight',
                'description' => 'A Fanciful Display of a Student And a Teacher who must Outgun a Crocodile in Nigeria',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_PUNK_DIVORCE,
                'title' => 'Punk Divorce',
                'description' => 'A Fast-Paced Tale of a Pastry Chef And a Boat who must Face a Frisbee in The Canadian Rockies',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_RIVER_OUTLAW,
                'title' => 'River Outlaw',
                'description' => 'A Thrilling Character Study of a Squirrel And a Lumberjack who must Face a Hunter in A MySQL Convention',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_SAINTS_BRIDE,
                'title' => 'Saints Bride',
                'description' => 'A Fateful Tale of a Technical Writer And a Composer who must Pursue a Explorer in The Gulf of Mexico',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_SHINING_ROSES,
                'title' => 'Shining Roses',
                'description' => 'A Awe-Inspiring Character Study of a Astronaut And a Forensic Psychologist who must Challenge a Madman in Ancient India',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
            (object) [
                'id' => self::ID_TIGHTS_DAWN,
                'title' => 'Tights Dawn',
                'description' => 'A Thrilling Epistle of a Boat And a Secret Agent who must Face a Boy in A Baloon',
                'release_year' => 2006,
                'language_id' => LanguageSeeder::ID_ENGLISH,
            ],
        ];
    }
}

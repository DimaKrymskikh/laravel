<?php

namespace Database\Seeders\Tests\Dvd;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActorSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    const ID_PENELOPE_GUINESS = 1;
    const ID_NICK_WAHLBERG = 2;
    const ID_ED_CHASE = 3;
    const ID_JENNIFER_DAVIS = 4;
    const ID_JOHNNY_LOLLOBRIGIDA = 5;
    
    const ID_NOT = 13;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'dvd.actors';
        
        foreach ($this->getActors() as $actor) {
            DB::table($tableName)->insert([
                'id' => $actor->id,
                'first_name' => $actor->first_name,
                'last_name' => $actor->last_name,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    private function getActors(): array
    {
        return [
            (object) [
                'id' => self::ID_PENELOPE_GUINESS,
                'first_name' => 'Penelope',
                'last_name' => 'Guiness',
            ],
            (object) [
                'id' => self::ID_NICK_WAHLBERG,
                'first_name' => 'Nick',
                'last_name' => 'Wahlberg',
            ],
            (object) [
                'id' => self::ID_ED_CHASE,
                'first_name' => 'Ed',
                'last_name' => 'Chase',
            ],
            (object) [
                'id' => self::ID_JENNIFER_DAVIS,
                'first_name' => 'Jennifer',
                'last_name' => 'Davis',
            ],
            (object) [
                'id' => self::ID_JOHNNY_LOLLOBRIGIDA,
                'first_name' => 'Johnny',
                'last_name' => 'Lollobrigida',
            ],
        ];
    }
}

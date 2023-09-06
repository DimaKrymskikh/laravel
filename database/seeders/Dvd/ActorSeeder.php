<?php

namespace Database\Seeders\Dvd;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Database\Copy\Dvd\ActorData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActorSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
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
    
    private function getActors(): iterable
    {
        yield from (new ActorData())();
    }
}

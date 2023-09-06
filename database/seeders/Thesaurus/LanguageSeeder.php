<?php

namespace Database\Seeders\Thesaurus;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Database\Copy\Thesaurus\LanguageData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'thesaurus.languages';
        
        foreach ($this->getLanguages() as $language) {
            DB::table($tableName)->insert([
                'id' => $language->id,
                'name' => $language->name,
            ]);
        }
        
        $this->setSequenceMaxValue($tableName);
    }
    
    private function getLanguages(): iterable
    {
        yield from (new LanguageData())();
    }
}

<?php

namespace Database\Seeders\Tests\Thesaurus;

use App\Contracts\Database\Sequences as SequencesInterface;
use App\Support\Database\Sequences;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder implements SequencesInterface
{
    use WithoutModelEvents, Sequences;
    
    const ID_ENGLISH = 1;
    const ID_RUSSIAN = 2;
    
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
    
    private function getLanguages(): array
    {
        return [
            (object) [
                'id' => self::ID_ENGLISH,
                'name' => 'Английский',
            ],
            (object) [
                'id' => self::ID_RUSSIAN,
                'name' => 'Русский',
            ],
        ];
    }
}

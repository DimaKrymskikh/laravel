<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait Sequences
{
    public function setSequenceMaxValue(string $tableName): int
    {
        $maxValue = DB::table($tableName)
                ->value(DB::raw('MAX(id)'));
        
        $seq = $tableName . '_id_seq';
        
        return DB::scalar("SELECT setval('$seq', $maxValue)");
    }
}

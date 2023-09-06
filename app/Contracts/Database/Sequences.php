<?php

namespace App\Contracts\Database;

interface Sequences
{
    public function setSequenceMaxValue(string $tableName): int;
}

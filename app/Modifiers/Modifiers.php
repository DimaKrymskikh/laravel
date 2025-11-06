<?php

namespace App\Modifiers;

use Illuminate\Database\Eloquent\Model;

abstract class Modifiers implements ModifiersInterface
{
    public function save(Model $model): void
    {
        $model->saveOrFail();
    }
    
    public function remove(Model $model): void
    {
        $model->deleteOrFail();
    }
}

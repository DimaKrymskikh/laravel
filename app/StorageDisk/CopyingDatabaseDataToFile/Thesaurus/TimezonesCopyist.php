<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus;

use App\Models\Thesaurus\Timezone;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TimezonesCopyist extends BaseCopyist
{
    /**
     * Записывает std-объект по модели Timezone
     * 
     * @param string $file
     * @param Timezone $timezone
     * @return void
     */
    public function writeData(string $file, Timezone $tz): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."(object) [");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'id' => $tz->id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'name' => '$tz->name',");
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."],");
    }
}

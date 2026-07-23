<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Dvd;

use App\Models\Dvd\Film;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilmsCopyist extends BaseCopyist
{
    /**
     * Записывает std-объект по модели Film
     * 
     * @param string $file
     * @param Film $film
     * @return void
     */
    public function writeData(string $file, Film $film): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."(object) [");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'id' => $film->id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'title' => '$film->title',");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'description' => '$film->description',");
        $releaseYear = $film->release_year ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'release_year' => $releaseYear,");
        $languageId = $film->language_id ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'language_id' => $languageId,");
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."],");
    }
}

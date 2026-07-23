<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus;

use App\Models\Thesaurus\Language;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LanguagesCopyist extends BaseCopyist
{
    /**
     * Записывает std-объект по модели Language
     * 
     * @param string $file
     * @param Language $language
     * @return void
     */
    public function writeData(string $file, Language $language): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."(object) [");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'id' => $language->id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'name' => '$language->name',");
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."],");
    }
}

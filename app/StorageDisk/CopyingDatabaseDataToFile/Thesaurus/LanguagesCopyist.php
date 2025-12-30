<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus;

use App\Models\Thesaurus\Language;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class LanguagesCopyist extends BaseCopyist implements LanguagesCopyistInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function writeData(string $file, Language $language): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."(object) [");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'id' => $language->id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'name' => '$language->name',");
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."],");
    }
}

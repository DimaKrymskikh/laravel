<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus;

use App\Models\Thesaurus\Language;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyistInterface;

interface LanguagesCopyistInterface extends BaseCopyistInterface
{
    /**
     * Записывает std-объект по модели Language
     * 
     * @param string $file
     * @param Language $language
     * @return void
     */
    public function writeData(string $file, Language $language): void;
}

<?php

namespace App\Modifiers\Thesaurus\Languages;

use App\Models\Thesaurus\Language;

interface LanguageModifiersInterface
{
    public function save(Language $language, string $name): void;
    
    public function delete(int $id): void;
}

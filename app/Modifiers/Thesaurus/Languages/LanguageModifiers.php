<?php

namespace App\Modifiers\Thesaurus\Languages;

use App\Models\Thesaurus\Language;

final class LanguageModifiers implements LanguageModifiersInterface
{
    public function save(Language $language, string $name): void
    {
        $language->name = $name;
        $language->save();
    }
    
    public function delete(int $id): void
    {
        Language::find($id)->delete();
    }
}

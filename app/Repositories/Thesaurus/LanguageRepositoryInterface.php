<?php

namespace App\Repositories\Thesaurus;

use App\Models\Thesaurus\Language;

interface LanguageRepositoryInterface
{
    public function exists(int $languageId): bool;
    
    public function save(Language $language, string $name): void;
    
    public function delete(int $id): void;
    
    public function getById(int $id): Language;
}

<?php

namespace App\Repositories\Thesaurus;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Models\Thesaurus\Language;
use Illuminate\Database\Eloquent\Collection;

interface LanguageRepositoryInterface
{
    public function save(Language $language, string $name): void;
    
    public function delete(int $id): void;
    
    public function getById(int $id): Language;
    
    public function getList(LanguageFilterDto $dto): Collection;
}

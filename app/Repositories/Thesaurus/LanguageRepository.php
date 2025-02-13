<?php

namespace App\Repositories\Thesaurus;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Models\Thesaurus\Language;
use Illuminate\Database\Eloquent\Collection;

final class LanguageRepository implements LanguageRepositoryInterface
{
    public function exists(int $languageId): bool
    {
        return Language::where('id', $languageId)->exists();
    }
    
    public function save(Language $language, string $name): void
    {
        $language->name = $name;
        $language->save();
    }
    
    public function delete(int $id): void
    {
        $this->getById($id)->delete();
    }
    
    public function getById(int $id): Language
    {
        return Language::find($id);
    }
    
    public function getList(LanguageFilterDto $dto): Collection
    {
        return Language::select('id', 'name')
                    ->filter($dto)
                    ->orderBy('name')
                    ->get();
    }
}

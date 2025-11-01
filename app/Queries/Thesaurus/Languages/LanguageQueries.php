<?php

namespace App\Queries\Thesaurus\Languages;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Exceptions\DatabaseException;
use App\Models\Thesaurus\Language;
use Illuminate\Database\Eloquent\Collection;

final class LanguageQueries implements LanguageQueriesInterface
{
    public function exists(int $id): bool
    {
        return Language::where('id', $id)->exists();
    }
    
    public function getById(int $id): Language
    {
        return Language::find($id) ?? throw new DatabaseException(sprintf(self::NOT_RECORD_WITH_ID, $id));
    }
    
    public function getList(): Collection
    {
        return $this->getListWithFilter(new LanguageFilterDto(''));
    }
    
    public function getListWithFilter(LanguageFilterDto $dto): Collection
    {
        return Language::select('id', 'name')
                    ->filter($dto)
                    ->orderBy('name')
                    ->get();
    }
}

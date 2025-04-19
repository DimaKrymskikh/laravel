<?php

namespace App\Queries\Thesaurus\Languages;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Models\Thesaurus\Language;
use Illuminate\Database\Eloquent\Collection;

final class LanguageQueries implements LanguageQueriesInterface
{
    public function getList(LanguageFilterDto $dto): Collection
    {
        return Language::select('id', 'name')
                    ->filter($dto)
                    ->orderBy('name')
                    ->get();
    }
}

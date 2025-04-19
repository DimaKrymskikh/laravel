<?php

namespace App\Queries\Thesaurus\Languages;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use Illuminate\Database\Eloquent\Collection;

interface LanguageQueriesInterface
{
    public function getList(LanguageFilterDto $dto): Collection;
}

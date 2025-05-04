<?php

namespace App\Queries\Thesaurus\Languages;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Queries\SimpleQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

interface LanguageQueriesInterface extends SimpleQueriesInterface
{
    public function getList(LanguageFilterDto $dto): Collection;
}

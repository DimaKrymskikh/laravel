<?php

namespace App\Queries\Thesaurus\Languages;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Queries\QueriesInterface;
use Illuminate\Database\Eloquent\Collection;

interface LanguageQueriesInterface extends QueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.languages' нет записи с id=%d";

    public function getListWithFilter(LanguageFilterDto $dto): Collection;
}

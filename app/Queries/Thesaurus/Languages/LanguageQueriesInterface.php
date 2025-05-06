<?php

namespace App\Queries\Thesaurus\Languages;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Queries\SimpleQueriesInterface;
use Illuminate\Database\Eloquent\Collection;

interface LanguageQueriesInterface extends SimpleQueriesInterface
{
    const NOT_RECORD_WITH_ID = "В таблице 'thesaurus.languages' нет записи с id=%d";

    public function getList(LanguageFilterDto $dto): Collection;
}

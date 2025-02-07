<?php

namespace Tests\Support\Data\Dto\Database\Thesaurus\Filters;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;

trait LanguageFilterDtoCase
{
    private function getBaseCaseLanguageFilterDto(): LanguageFilterDto
    {
        return new LanguageFilterDto('TestName');
    }
}

<?php

namespace App\Http\Requests\Thesaurus\Filters;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Http\Requests\PaginatorRequest;

class LanguageFilterRequest extends PaginatorRequest
{
    public function getLanguageFilterDto(): LanguageFilterDto
    {
        return new LanguageFilterDto($this->query('name_filter'));
    }
}

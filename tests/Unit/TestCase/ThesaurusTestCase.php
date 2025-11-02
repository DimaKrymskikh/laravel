<?php

namespace Tests\Unit\TestCase;

use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use PHPUnit\Framework\TestCase;

abstract class ThesaurusTestCase extends TestCase
{
    protected function getLanguageFilterDto(): LanguageFilterDto
    {
        return new LanguageFilterDto('TestName');
    }
}

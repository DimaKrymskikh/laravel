<?php

namespace Tests\Unit\ValueObjects\Pagination;

use App\Support\Pagination\Paginator;
use App\ValueObjects\Pagination\PerPageValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PerPageValueTest extends TestCase
{
    public static function correctPerPagesProvider(): array
    {
        return [
            [10, '10'],
            [20, ' 20 '],
            [100, '100'],
            // Строка начинается с последовательности цифр
            [50, '50x'],
        ];
    }
    
    public static function inCorrectPerPagesProvider(): array
    {
        return [
            [null],
            [''],
            ['7777777777777777777777777777777777777777777777777777777'],
            // Отсутствует в Paginator::PAGINATOR_PER_PAGE_LIST
            ['12'],
            ['-7'],
            // Строка начинается с буквы
            ['x100'],
        ];
    }
    
    #[DataProvider('correctPerPagesProvider')]
    public function test_correct_per_pages(int $page, string $str): void
    {
        $this->assertEquals($page, PerPageValue::create($str)->value);
    }
    
    #[DataProvider('inCorrectPerPagesProvider')]
    public function test_incorrect_per_pages(?string $str): void
    {
        $this->assertEquals(Paginator::PAGINATOR_DEFAULT_PER_PAGE, PerPageValue::create($str)->value);
    }
}

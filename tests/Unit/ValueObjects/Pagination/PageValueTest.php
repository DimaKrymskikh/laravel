<?php

namespace Tests\Unit\ValueObjects\Pagination;

use App\Support\Pagination\Paginator;
use App\ValueObjects\Pagination\PageValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PageValueTest extends TestCase
{
    public static function correctPagesProvider(): array
    {
        return [
            [12, '12'],
            [15, ' 15 '],
            // Строка начинается с последовательности цифр
            [71, '71x'],
            [50, '50x3'],
        ];
    }
    
    public static function inCorrectPagesProvider(): array
    {
        return [
            [null],
            [''],
            ['-7'],
            ['7777777777777777777777777777777777777777777777777777777'],
            // Строка начинается с буквы
            ['x7'],
        ];
    }
    
    #[DataProvider('correctPagesProvider')]
    public function test_correct_pages(int $page, string $str): void
    {
        $this->assertEquals($page, PageValue::create($str)->value);
    }
    
    #[DataProvider('inCorrectPagesProvider')]
    public function test_incorrect_pages(?string $str): void
    {
        $this->assertEquals(Paginator::PAGINATOR_DEFAULT_CURRENT_PAGE, PageValue::create($str)->value);
    }
}

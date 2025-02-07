<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\DateString;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateStringTest extends TestCase
{
    public static function correctDateStringProvider(): array
    {
        return [
            ['12.01.2025', '12.01.2025'],
            ['03.12.2024', '    03.12.2024    '],
            ['05.11.2020', '05.11.2020'],
            // Високосный год
            ['29.02.2024', '29.02.2024'],
            // В фильтре условие будет пропущено
            ['', null],
            ['', ''],
        ];
    }
    
    public static function inCorrectDateStringProvider(): array
    {
        return [
            ['-7'],
            ['foo'],
            ['a13.12.2024'],
            ['31.04.2020'],
            ['32.05.2020'],
            ['11.13.2020'],
            // Не високосный год
            ['29.02.2023'],
        ];
    }
    
    #[DataProvider('correctDateStringProvider')]
    public function test_correct_dates(string $result, ?string $date): void
    {
        $this->assertEquals($result, DateString::create($date)->value);
    }
    
    #[DataProvider('inCorrectDateStringProvider')]
    public function test_incorrect_dates(string $date): void
    {
        $this->assertEquals(now()->format('d.m.Y'), DateString::create($date)->value);
    }
}

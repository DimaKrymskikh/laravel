<?php

namespace Tests\Unit\ValueObjects\Date;

use App\ValueObjects\Date\TimeStringFromSeconds;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TimeStringFromSecondsTest extends TestCase
{
    public static function defaultDecProvider(): array
    {
        return [
            [-1.1, '0 с.'],
            [0, '0 с.'],
            [0.235, '00.24 с.'],
            [5.241, '05.24 с.'],
            [60, '01 м.'],
            [60.2, '01 м. 00.2 с.'],
            [61, '01 м. 01 с.'],
            [3600 * 2 + 60 * 45 + 11.6666666, '2 ч. 45 м. 11.67 с.'],
            [3600 * 3, '3 ч.'],
        ];
    }
    
    #[DataProvider('defaultDecProvider')]
    public function test_time_string_with_default_dec(float $seconds, string $value): void
    {
        $this->assertEquals($value, TimeStringFromSeconds::create($seconds)->value);
    }
    public static function decProvider(): array
    {
        return [
            [0.235135, -1, '00.24 с.'],
            [0.235135, 0, '0 с.'],
            [60.235135, 0, '01 м.'],
            [0.535135, 0, '01 с.'],
            [1.235135, 0, '01 с.'],
            [0.235135, 1, '00.2 с.'],
            [0.235135, 2, '00.24 с.'],
            [0.235135, 3, '00.235 с.'],
            [0.235135, 4, '00.2351 с.'],
            [0.235135, 7, '00.24 с.'],
        ];
    }
    
    #[DataProvider('decProvider')]
    public function test_time_string_with_changeable_dec(float $seconds, int $dec, string $value): void
    {
        $this->assertEquals($value, TimeStringFromSeconds::create($seconds, $dec)->value);
    }
}

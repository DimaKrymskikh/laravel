<?php

namespace Tests\Unit\ValueObjects\ArrayItems;

use App\ValueObjects\ArrayItems\TimeInterval;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TimeIntervalTest extends TestCase
{
    public static function successProvider(): array
    {
        return [
            ['day', 'day'],
            ['week', ' week '],
            ['month', ' month'],
            ['year', 'year'],
            [TimeInterval::DEFAULT_INTERVAL, null],
            [TimeInterval::DEFAULT_INTERVAL, ''],
            [TimeInterval::DEFAULT_INTERVAL, ' '],
            [TimeInterval::DEFAULT_INTERVAL, 'fail'],
        ];
    }
    
    #[DataProvider('successProvider')]
    public function test_success_time_interval(string $result, string|null $value): void
    {
        $this->assertEquals($result, TimeInterval::create($value)->value);
    }
}

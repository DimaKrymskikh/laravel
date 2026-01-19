<?php

namespace App\DataTransferObjects\Database\OpenWeather\Filters;

use App\ValueObjects\Date\DateString;

final readonly class WeatherFilterDto
{
    public function __construct(
            public DateString $datefrom,
            public DateString $dateto,
    ) {
    }
}

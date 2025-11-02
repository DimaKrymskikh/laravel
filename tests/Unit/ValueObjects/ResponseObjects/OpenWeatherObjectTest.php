<?php

namespace Tests\Unit\ValueObjects\ResponseObjects;

use App\ValueObjects\ResponseObjects\OpenWeatherObject;
use Database\Testsupport\OpenWeather\OpenWeatherResponse;
use PHPUnit\Framework\TestCase;

class OpenWeatherObjectTest extends TestCase
{
    use OpenWeatherResponse;
    
    public function test_response_object(): void
    {
        $this->assertEqualsCanonicalizing((object) [
            'weatherDescription' => 'Хорошая погода',
            'mainTemp'  => 11.7,
            'mainFeelsLike' => null,
            'mainPressure' => 1000,
            'mainHumidity' => 77,
            'visibility' => 500,
            'windSpeed' => 2.5,
            'windDeg' => 120,
            'cloudsAll' => 100,
        ], OpenWeatherObject::create( (object) $this->getWeatherForOneCity() )->data);
    }
}

<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\OpenWeather;

use App\Models\OpenWeather\Weather;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class WeatherCopyist extends BaseCopyist implements WeatherCopyistInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function writeData(string $file, Weather $weather): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'id' => $weather->id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'city_id' => $weather->city_id,");
        $weatherDescription = $weather->weather_description ? "'$weather->weather_description'" : 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'weather_description' => $weatherDescription,");
        $mainTemp = $weather->main_temp ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'main_temp' => $mainTemp,");
        $mainFeelsLike = $weather->main_feels_like ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'main_feels_like' => $mainFeelsLike,");
        $mainPressure = $weather->main_pressure ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'main_pressure' => $mainPressure,");
        $mainHumidity = $weather->main_humidity ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'main_humidity' => $mainHumidity,");
        $visibility = $weather->visibility ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'visibility' => $visibility,");
        $windSpeed = $weather->wind_speed ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'wind_speed' => $windSpeed,");
        $windDeg = $weather->wind_deg ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'wind_deg' => $windDeg,");
        $cloudsAll = $weather->clouds_all ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'clouds_all' => $cloudsAll,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'created_at' => '$weather->created_at',");
        Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
    }
}

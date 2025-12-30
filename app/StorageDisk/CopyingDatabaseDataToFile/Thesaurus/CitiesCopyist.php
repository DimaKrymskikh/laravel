<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Thesaurus;

use App\Models\Thesaurus\City;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CitiesCopyist extends BaseCopyist implements CitiesCopyistInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function writeData(string $file, City $city): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'id' => $city->id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'name' => '$city->name',");
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'open_weather_id' => $city->open_weather_id,");
        $timezoneId = $city->timezone_id ?? 'null';
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'timezone_id' => $timezoneId,");
        Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
    }
}

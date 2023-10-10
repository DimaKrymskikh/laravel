<?php

namespace App\Support\Support;

use App\Contracts\Support\Timezone as TimezoneInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

trait Timezone
{
    public function setTimezone(Collection $items): void
    {
        foreach($items as $item) {
            // Пропускаем объект, если он не связан с таблицей open_weather.weather
            // Например, для города ещё не получены данные о погоде
            if(!$item->weatherFirst) {
                continue;
            }
            $tzName = $item->timezone_id ? $item->timezone->name : TimezoneInterface::DEFAULT_TIMEZONE_NAME;
            $item->weatherFirst->created_at = Carbon::parse($item->weatherFirst->created_at)->setTimezone($tzName);
        }
    }
}

<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;

class CarbonService
{
    const DEFAULT_TIMEZONE_NAME = 'Asia/Krasnoyarsk';
    
    /**
     * Для временной метки устанавливаем новый часовой пояс.
     * 
     * @param CarbonImmutable $time
     * @param string $tzName
     * @return Carbon
     */
    public static function setNewTimezone(CarbonImmutable $time, string $tzName): Carbon
    {
        return Carbon::parse($time)->setTimezone($tzName);
    }
}

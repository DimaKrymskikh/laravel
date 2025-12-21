<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;

final class CarbonService
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
    
    /**
     * В строке даты и времени изменяет часовой пояс.
     * 
     * @param string $date Валидная для даты и времени строка.
     * @param string $oldTzName Старый часовой пояс, например, 'UTC'.
     * @param string $newTzName Новый часовой пояс, например, 'Asia/Krasnoyarsk'.
     * @param type $format Формат даты, например, 'H:i:s d.m.Y'.
     * @return string Строка даты и времени.
     */
    public static function setNewTimezoneInString(string $date, string $oldTzName, string $newTzName, $format): string
    {
        return Carbon::parse(trim($date), $oldTzName)->setTimezone($newTzName)->format($format);
    }
}

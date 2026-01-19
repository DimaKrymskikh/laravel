<?php

namespace App\ValueObjects\Date;

/**
 * Содержит строку времени формата 'k ч. ll м. mm.nn с.'.
 * Используется для отрисовки времени выполнения, например, команд.
 */
final readonly class TimeStringFromSeconds
{
    public string $value;
    
    private function __construct(float $unixSeconds, int $dec = 2)
    {
        if($unixSeconds <= 0) {
            $this->value = '0 с.';
            return;
        }
        
        if($dec < 0 || $dec > 6) {
            $dec = 2;
        }
        
        $intSeconds = floor($unixSeconds);
        $hours = floor($intSeconds / 3600);
        
        $intSecondsInHours = $hours * 3600;
        $minutes = floor(($intSeconds - $intSecondsInHours) / 60);
        
        $intSecondsInHoursAndMinutes = $intSecondsInHours + $minutes * 60;
        $seconds = round($unixSeconds - $intSecondsInHoursAndMinutes, $dec);
        
        $strTime = '';
        if($hours > 0) {
            $strTime .= "$hours ч. ";
        }
        
        if($minutes > 0) {
            $strTime .= sprintf('%02d', $minutes).' м. ';
        }
        
        if($seconds > 0) {
            [$iPart, $fPart] = explode('.', (string) $seconds);
            $strTime .= sprintf("%02d", (int) $iPart);
            $strTime .= $fPart ? '.'.sprintf("%d", (int) $fPart) : '';
            $strTime .= ' с.';
        }
        
        $finalStr = trim($strTime);
        // Если $finalStr === '' (такой случай может быть при $dec = 0), то $this->value = '0 с.'
        $this->value = $finalStr ? $finalStr : '0 с.';
    }
    
    /**
     * По полученным секундам создаёт строку времени формата 'k ч. ll м. mm.nn с.'.
     * 
     * @param float $seconds Секунды.
     * @param int $dec Число десятичных знаков после запятой.
     * @return self
     */
    public static function create(float $seconds, int $dec = 2): self
    {
        return new self($seconds, $dec);
    }
}

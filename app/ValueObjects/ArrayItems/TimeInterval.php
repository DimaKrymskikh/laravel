<?php

namespace App\ValueObjects\ArrayItems;

/**
 * Хранит временной интервал.
 */
final readonly class TimeInterval
{
    public const TIME_INTERVALS = ['day', 'week', 'month', 'year'];
    public const DEFAULT_INTERVAL = 'month';
    
    public string $value;
    
    private function __construct(string|null $interval)
    {
        $strInterval = trim($interval ?? '');
        
        if(collect(self::TIME_INTERVALS)->doesntContain($strInterval)) {
            $this->value = self::DEFAULT_INTERVAL;
            return;
        }
        
        $this->value = $strInterval;
    }
    
    /**
     * Проверяет, что $interval принадледит массиву TIME_INTERVALS, и сохраняет это значение в $value.
     * Если $interval не принадледит массиву TIME_INTERVALS, то в $value сохраняется дефолтное значение DEFAULT_INTERVAL.
     * 
     * @param string|null $interval
     * @return self
     */
    public static function create(string|null $interval): self
    {
        return new self($interval);
    }
}

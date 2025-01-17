<?php

namespace App\ValueObjects;

final readonly class DateString
{
    public string $value;

    private function __construct(?string $date)
    {
        $str = trim($date ?? '');
        
        // В фильтрах запроса не будет проверки
        if (!$str) {
            $this->value = $str;
            return ;
        }
        
        if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $str)) {
            $this->value = now()->format('d.m.Y');
            return ;
        }
        
        [$day, $month, $year] = explode('.', $str);
        
        if (!checkdate($month, $day, $year)) {
            $this->value = now()->format('d.m.Y');
            return ;
        }
        
        $this->value = $str;
    }
    
    public static function create(?string $date): self
    {
        return new self($date);
    }
}

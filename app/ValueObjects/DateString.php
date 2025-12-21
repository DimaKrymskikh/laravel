<?php

namespace App\ValueObjects;

/**
 * Проверяет, что строка может быть использована для задания даты в формате 'd.m.Y'.
 * Класс хранит валидную строку времени $value.
 */
final readonly class DateString
{
    public string $value;

    private function __construct(string|null $date, bool $isForFilter)
    {
        $str = trim($date ?? '');
        $now = now()->format('d.m.Y');
        
        if (!$str) {
            $this->value = $isForFilter ? $str : $now;
            return ;
        }
        
        if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $str)) {
            $this->value = $now;
            return ;
        }
        
        [$day, $month, $year] = explode('.', $str);
        
        if (!checkdate($month, $day, $year)) {
            $this->value = $now;
            return ;
        }
        
        $this->value = $str;
    }
    
    /**
     * Создаёт валидную строку времени $value.
     * Если $date = null или '', свойство $value зависит от $isForFilter.
     * Если $isForFilter = true (свойство будет использовано в фильтрах запроса), то $value = '', чтобы условие where в запросе было пропущено.
     * Если $isForFilter = false, то $value принимает текущее время.
     * 
     * @param string|null $date Строка даты|времени.
     * @param bool $isForFilter Будет ли использоватся свойство $value в фильтрах.
     * @return self
     */
    public static function create(string|null $date, bool $isForFilter = true): self
    {
        return new self($date, $isForFilter);
    }
}

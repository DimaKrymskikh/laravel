<?php

namespace App\Queries\OpenWeather;

interface OpenWeatherQueriesInterface
{
    public const NUMBER_OF_ITEMS_IN_CHUNCK = 2;
    
    /**
     * Извлекает по частям все данные таблицы 'open_weather.weather'.
     * Используется метод 'lazyById'
     * 
     * @param \Closure $callback
     * @return void
     */
    public function getListInLazyById(\Closure $callback): void;
}

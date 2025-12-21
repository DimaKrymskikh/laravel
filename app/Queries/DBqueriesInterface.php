<?php

namespace App\Queries;

interface DBqueriesInterface
{
    /**
     * Оболочка над DB::select.
     * 
     * @param string $query Строка запроса.
     * @param array $options Параметры запроса, привязанные к строке запроса.
     * @return array
     */
    public function getArray(string $query, array $options = []): array;
}

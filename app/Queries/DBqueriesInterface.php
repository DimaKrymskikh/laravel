<?php

namespace App\Queries;

use App\Services\DatabaseQueryInterface;

interface DBqueriesInterface extends DatabaseQueryInterface
{
    /**
     * Оболочка над DB::select.
     * 
     * @param string $query Строка запроса.
     * @param array $options Параметры запроса, привязанные к строке запроса.
     * @return array Массив std-объектов.
     */
    public function getArray(string $query, array $options = []): array;
    
    /**
     * Оболочка над DB::select.
     * Метод предназначен для извлечения одной строки таблицы.
     * Если запрос извлекает несколько строк, то будет возвращён первый объект.
     * Если запрос не извлечёт ни одной строки, то будет возвращён пустой объект: (object) [].
     * 
     * @param string $query Строка запроса.
     * @param array $options Параметры запроса, привязанные к строке запроса.
     * @return object std-объект.
     */
    public function getObject(string $query, array $options = []): object;
    
    /**
     * Оболочка над DB::scalar.
     * 
     * @param string $query
     * @param array $options
     * @return mixed
     */
    public function getValue(string $query, array $options = []): mixed;
}

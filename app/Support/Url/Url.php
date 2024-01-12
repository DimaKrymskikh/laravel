<?php

namespace App\Support\Url;

trait Url
{
    /**
     * Формирует url для get-запроса
     * 
     * @param string $url - базовый url
     * @param array $data
     * @return string
     */
    private function getUrl(string $url, array $data = []): string
    {
        $params = http_build_query($data);
        // Знак вопроса пишем только тогда, когда имеются параметры запроса
        $sign = $params ? '?' : '';
                
        return "$url$sign$params";
    }
}

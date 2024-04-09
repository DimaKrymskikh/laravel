<?php

return [
    'cityName' => [
        'required' => 'Название города не может быть пустым.',
        'string' => 'Название города должно быть строкой.'
    ],
    'openWeatherId' => [
        'required' => 'Id города в сервисе OpenWeather не может быть пустым.',
        'unique' => 'Введённый id города для сервиса OpenWeather уже связан с другим городом.',
        'integer' => 'Id города в сервисе OpenWeather должен быть целым числом.',
        'exist' => 'В таблице "thesaurus.cities" нет городов с полем open_weather_id = :openWeatherId',
    ]
];

<?php

namespace Database\Copy\Thesaurus;

// Данные таблицы thesaurus.languages
class LanguageData
{
    public function __invoke(): array
    {
        return [
            (object) [
                'id' => 1,
                'name' => 'English',
            ],
            (object) [
                'id' => 2,
                'name' => 'Italian',
            ],
            (object) [
                'id' => 3,
                'name' => 'Japanese',
            ],
            (object) [
                'id' => 4,
                'name' => 'Mandarin',
            ],
            (object) [
                'id' => 5,
                'name' => 'French',
            ],
            (object) [
                'id' => 6,
                'name' => 'German',
            ],
        ];
    }
}

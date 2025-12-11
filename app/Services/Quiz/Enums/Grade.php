<?php

namespace App\Services\Quiz\Enums;

/**
 * Оценки, которые могут получить пользователи
 */
enum Grade: string
{
    // Значения соответствуют табличному типу quiz.grade
    case Fail = 'неудовлетворительно';
    case Satisfactory = 'удовлетворительно';
    case Good = 'хорошо';
    case Excellent = 'отлично';
}

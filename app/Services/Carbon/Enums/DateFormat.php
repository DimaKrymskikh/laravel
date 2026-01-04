<?php

namespace App\Services\Carbon\Enums;

/**
 * Форматы дат
 */
enum DateFormat: string
{
    case Full = 'H:i:s d.m.Y';
    case Date = 'd.m.Y';
}

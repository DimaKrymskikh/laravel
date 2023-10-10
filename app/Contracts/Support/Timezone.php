<?php

namespace App\Contracts\Support;

use Illuminate\Database\Eloquent\Collection;

interface Timezone
{
    const DEFAULT_TIMEZONE_NAME = 'Asia/Krasnoyarsk';
    
    public function setTimezone(Collection $model): void;
}

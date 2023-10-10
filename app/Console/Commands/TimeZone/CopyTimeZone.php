<?php

namespace App\Console\Commands\TimeZone;

use Carbon\CarbonTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyTimeZone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:timezone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение/Обновление таблицы thesaurus.timezones';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Старт.');
        $this->line("$this->description\n");
        
        $timezones = CarbonTimeZone::listIdentifiers();
        
        foreach ($timezones as $timezone) {
            DB::table('thesaurus.timezones')->insertOrIgnore([
                'name' => $timezone,
            ]);
        }
        
        $this->info('Команда выполнена.');
    }
}

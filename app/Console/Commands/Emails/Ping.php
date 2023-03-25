<?php

namespace App\Console\Commands\Emails;

use App\Mail\Ping as PingEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class Ping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:ping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка почтового сообщения';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Mail::to(env('MAIL_TO_ADDRESS'))->send(new PingEmail);
    }
}

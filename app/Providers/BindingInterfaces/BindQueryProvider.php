<?php

namespace App\Providers\BindingInterfaces;

use App\Contracts\Database\DBqueriesInterface;
use App\Queries\DBqueries;
use Illuminate\Support\ServiceProvider;

class BindQueryProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DBqueriesInterface::class, DBqueries::class);
    }
}

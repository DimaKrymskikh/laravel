<?php

namespace App\Providers\BindingInterfaces;

use App\Services\ServiceManager;
use App\Services\ServiceManagerInterface;
use Illuminate\Support\ServiceProvider;

class BindServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ServiceManagerInterface::class, ServiceManager::class);
    }
}

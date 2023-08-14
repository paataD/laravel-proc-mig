<?php

namespace AtLab\ProcMig;
use AtLab\ProcMig\Traits\PublishesMigrations;
use AtLab\ProcMig\Console\Commands\CreateNewProcedure;
use AtLab\ProcMig\Console\Commands\ProcedureMigrate;
use Illuminate\Support\ServiceProvider;

class ProcMigServiceProvider extends ServiceProvider
{
    Use PublishesMigrations;
    public function boot(){
        $this->registerMigrations(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateNewProcedure::class,
                ProcedureMigrate::class
            ]);
        }
    }
}

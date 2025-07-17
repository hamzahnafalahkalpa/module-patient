<?php

namespace Hanafalah\ModulePatient\Providers;

use Illuminate\Support\ServiceProvider;
use Hanafalah\ModulePatient\Commands;

class CommandServiceProvider extends ServiceProvider
{
    protected $__commands = [];

    public function register()
    {
        $this->commands(config('module-patient.commands', $this->__commands));
    }

    public function provides()
    {
        return $this->__commands;
    }
}

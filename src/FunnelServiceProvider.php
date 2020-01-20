<?php

namespace TanmayMishu\LaravelFunnel;

use Illuminate\Support\ServiceProvider;
use TanmayMishu\LaravelFunnel\Console\FilterCommand;

class FunnelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilterCommand::class,
            ]);
        }
    }

    public function register()
    {

    }
}

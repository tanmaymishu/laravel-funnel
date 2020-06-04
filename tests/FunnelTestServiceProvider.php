<?php

namespace TanmayMishu\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use TanmayMishu\LaravelFunnel\Console\FilterCommand;
use TanmayMishu\Tests\Models\Post;

class FunnelTestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilterCommand::class,
            ]);
        }

        Route::middleware('api')->group(function($router) {
            $router->get('/posts', function() {
                return response()->json(['posts' => Post::filtered()->get()]);
            });
        });
    }
}

<?php

namespace TanmayMishu\LaravelFunnel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
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

        $this->publishes([
            __DIR__.'/../config/funnel.php' => base_path('config/funnel.php'),
        ]);

        /**
         * This macro enables Funnel to catch the RelationNotFoundException
         * when a missing relationship gets passed via the query string.
         * Use the good old get(), if you want to handle it manually.
         */
        Builder::macro('getEagerSafe', function () {
            try {
                return $this->get();
            } catch (RelationNotFoundException $exception) {
                throw ValidationException::withMessages([
                    'message' => $exception->getMessage(),
                ]);
            }
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/funnel.php', 'funnel');
    }
}

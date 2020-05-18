<?php

namespace TanmayMishu\LaravelFunnel;

use Illuminate\Pipeline\Pipeline;

trait HasFilters
{
    public static function filtered()
    {
        return app(Pipeline::class)
            ->send(static::query())
            ->through((new static)->filters)
            ->thenReturn();
    }
}

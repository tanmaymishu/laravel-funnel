<?php

namespace TanmayMishu\LaravelFunnel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

trait HasFilters
{
    /**
     * Run the filters.
     *
     * @return Builder
     */
    public static function filtered(): Builder
    {
        return app(Pipeline::class)
            ->send(static::query())
            ->through((new static)->filters)
            ->thenReturn();
    }
}

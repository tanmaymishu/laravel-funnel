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
        $query = static::query();
        $eagerKey = config()->has('funnel')
            ? config('funnel.eager_key')
            : 'with';

        if (request()->has($eagerKey)) {
            $query->with(collect(explode(',', request($eagerKey)))->filter(function ($eager) {
                if (str_contains($eager, '.')) {
                    return method_exists(static::class, explode('.', $eager)[0]);
                }

                return method_exists(static::class, $eager);
            })->toArray());
        }

        return app(Pipeline::class)
            ->send($query)->through((new static)->filters)
            ->thenReturn();
    }
}

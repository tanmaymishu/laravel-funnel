<?php

namespace TanmayMishu\LaravelFunnel;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    public function handle($passable, \Closure $next)
    {
        if (! request()->has($this->parameter)) {
            return $next($passable);
        }

        $builder = $next($passable);

        return $this->apply($builder);
    }

    abstract protected function apply(Builder $builder): Builder;
}

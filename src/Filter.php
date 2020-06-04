<?php

namespace TanmayMishu\LaravelFunnel;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Execute the pipe.
     *
     * @param $passable
     * @param  \Closure  $next
     * @return Builder|mixed
     */
    public function handle($passable, \Closure $next)
    {
        if (! request()->has($this->parameter)) {
            return $next($passable);
        }

        $builder = $next($passable);

        return $this->apply($builder);
    }

    /**
     * Prepare the builder. Customize this according to your need.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    abstract protected function apply(Builder $builder): Builder;
}

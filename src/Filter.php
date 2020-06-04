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

    /**
     * Get the default builder for a typical where clause. For multi-value
     * params, param values will be passed through `OR` sub-queries.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    protected function getDefaultWhereBuilder(Builder $builder): Builder
    {
        $paramValue = $this->getParamValue();

        if (is_array($paramValue)) {
            $builder = $builder->where(function ($q) use ($paramValue) {
                for ($i = 0; $i < count($paramValue); $i++) {
                    if ($i == 0) {
                        $q->where($this->attribute, $this->operator, $paramValue[$i]);
                    } else {
                        $q->orWhere($this->attribute, $this->operator, $paramValue[$i]);
                    }
                }
            });

            return $builder;
        }

        return $builder->where($this->attribute, $this->operator, $paramValue);
    }

    /**
     * Get the parameter's value. If the parameter is an array
     * and the operator is like/LIKE, then surround the values
     * with `%` and return the modified array.
     *
     * @return string|array
     */
    protected function getParamValue()
    {
        $params = request($this->parameter);

        if (is_array($params) && ($this->operator == 'LIKE' || $this->operator == 'like')) {
            $params = array_map(function ($param) {
                return '%'.$param.'%';
            }, $params);

            return $params;
        }

        return $this->operator == 'LIKE' || $this->operator == 'like'
            ? '%'.$params.'%'
            : $params;
    }
}

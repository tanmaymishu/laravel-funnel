<?php

namespace TanmayMishu\LaravelFunnel;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * @var RequestParameter
     */
    protected $requestParam;

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
     * Get the parameter's value. If the the operator is like/LIKE
     * then surround the value with `%` and if the parameter is an
     * array or comma-delimited list, then convert it to array.
     *
     * @return string|array
     */
    protected function getParamValue()
    {
        $this->requestParam = new RequestParameter(request($this->parameter));

        if ($this->expectsSearch() && $this->requestParam->isArray()) {
            return $this->requestParam->mapToLikeFriendly();
        }

        if ($this->expectsSearch()) {
            return $this->requestParam->toLikeFriendly();
        }

        if ($this->requestParam->isMultiValue()) {
            return $this->requestParam->toArray();
        }

        return $this->requestParam->params;
    }

    /**
     * Checks whether the operator is a LIKE operator.
     *
     * @return bool
     */
    protected function expectsSearch(): bool
    {
        return $this->operator == 'LIKE' || $this->operator == 'like';
    }
}

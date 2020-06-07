<?php

namespace TanmayMishu\LaravelFunnel;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * @var string
     */
    protected $parameter;

    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var RequestParameter
     */
    protected $requestParam;

    /**
     * @var string|array
     */
    protected $paramValue;

    /**
     * @var string
     */
    protected $relation;

    /**
     * @var Builder
     */
    protected $builder;

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
     * Set the request parameter.
     *
     * @param  RequestParameter  $requestParam
     * @return Filter
     */
    protected function setRequestParam(RequestParameter $requestParam): Filter
    {
        $this->requestParam = $requestParam;
        return $this;
    }

    /**
     * Get the default builder for a typical where clause. For relational
     * attr, query will run inside a `whereHas()` method.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    protected function getDefaultWhereBuilder(Builder $builder): Builder
    {
        if ($this->hasRelation()) {
            return $builder->whereHas($this->extractRelation(), function ($builder) {
                $this->setAttribute($this->extractAttribute())
                    ->prepareWhereBuilder($builder);
            });
        }

        return $this->prepareWhereBuilder($builder);
    }

    /**
     * Checks whether the attribute contains a relation.
     *
     * @return bool
     */
    protected function hasRelation(): bool
    {
        return $this->relationCount() > 1;
    }

    /**
     * Get the number of relations (dot separated).
     *
     * @return int
     */
    protected function relationCount(): int
    {
        return count(explode('.', $this->attribute));
    }

    /**
     * Get the relation in string.
     *
     * @return string
     */
    protected function extractRelation(): string
    {
        if (! $this->hasRelation()) {
            throw new \RuntimeException('Trying to extract relation from non-relation string.');
        }

        $exploded = explode('.', $this->attribute);

        return implode('.', array_slice(
            $exploded, 0, count($exploded) - 1, true
        ));
    }

    /**
     * Prepare the where builder for a typical where clause. For multi-value
     * params, param values will be passed through `OR` sub-queries.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    protected function prepareWhereBuilder(Builder $builder): Builder
    {
        $this->paramValue = $this->getParamValue();

        if (is_array($this->paramValue)) {
            $builder = $builder->where(function ($subBuilder) {
                $this->setBuilder($subBuilder);
                collect($this->paramValue)->each(function ($value, $index) {
                    $index == 0
                        ? $this->buildMultiValueQuery($value)
                        : $this->buildMultiValueQuery($value, true);
                });
            });
            return $builder;
        }

        return $this->setBuilder($builder)->buildSingleValueQuery();
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
        $this->setRequestParam(new RequestParameter(request($this->parameter)));

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
        return strtolower($this->operator) == 'like';
    }

    /**
     * Set the builder.
     *
     * @param  Builder  $builder
     * @return Filter
     */
    protected function setBuilder(Builder $builder): Filter
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * Build query when paramValue is an array.
     *
     * @param $value
     * @param  bool  $forOr
     * @return Builder
     */
    protected function buildMultiValueQuery($value, bool $forOr = false): Builder
    {
        return $forOr ? $this->buildOrWhereQuery($value) : $this->buildWhereQuery($value);
    }

    /**
     * Build orWhere query. If no $value is passed, paramValue
     * is used as default.
     *
     * @param  string|null  $value
     * @return Builder
     */
    protected function buildOrWhereQuery(string $value = null): Builder
    {
        return $this->builder->orWhere($this->attribute, $this->operator, $value ?: $this->paramValue);
    }

    /**
     * Build where query. If no $value is passed, paramValue
     * is used as default.
     *
     * @param  string|null  $value
     * @return Builder
     */
    protected function buildWhereQuery(string $value = null): Builder
    {
        return $this->builder->where($this->attribute, $this->operator, $value ?: $this->paramValue);
    }

    /**
     * Build where query when paramValue is non-array.
     *
     * @return Builder
     */
    protected function buildSingleValueQuery(): Builder
    {
        return $this->buildWhereQuery();
    }

    /**
     * Set the attribute.
     *
     * @param  string  $attribute
     * @return Filter
     */
    protected function setAttribute(string $attribute): Filter
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Isolate and extract the attribute from relation.
     *
     * @return string
     */
    protected function extractAttribute(): string
    {
        if (! $this->hasRelation()) {
            throw new \RuntimeException('Trying to extract attribute from non-relation string.');
        }

        $exploded = explode('.', $this->attribute);
        return $exploded[count($exploded) - 1];
    }
}

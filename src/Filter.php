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
     * @var Parameter
     */
    private $mParameter;

    /**
     * @var Attribute
     */
    private $mAttribute;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * Execute the pipe.
     *
     * @param $passable
     * @param  \Closure  $next
     * @return Builder|mixed
     */
    public function handle($passable, \Closure $next)
    {
        if (! request()->filled($this->parameter)) {
            return $next($passable);
        }

        $this->checkParamCollision();

        $this->builder = $next($passable);

        return $this->apply($this->builder);
    }

    /**
     * Prepare the builder. Customize this according to your need.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    abstract protected function apply(Builder $builder): Builder;

    public function checkParamCollision(): void
    {
        $eagerKey = config()->has('funnel')
            ? config('funnel.eager_key')
            : 'with';

        if ($this->parameter == $eagerKey) {
            throw new \RuntimeException('Reserved parameter. Please provide a different parameter.');
        }
    }

    /**
     * Get the attribute.
     *
     * @return Attribute
     */
    protected function getAttribute(): Attribute
    {
        return $this->mAttribute;
    }

    /**
     * Set the attribute.
     *
     * @param  Attribute  $mAttribute
     * @return $this
     */
    protected function setAttribute(Attribute $mAttribute)
    {
        $this->mAttribute = $mAttribute;

        return $this;
    }

    /**
     * Get current builder.
     *
     * @return Builder
     */
    protected function getBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * Set the builder.
     *
     * @param  Builder  $builder
     * @return Filter
     */
    protected function setBuilder(Builder $builder): self
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Get the parameter.
     *
     * @return Parameter
     */
    protected function getParameter(): Parameter
    {
        return $this->mParameter;
    }

    /**
     * Set the request parameter.
     *
     * @param  Parameter  $mParameter
     * @return Filter
     */
    protected function setParameter(Parameter $mParameter): self
    {
        $this->mParameter = $mParameter;

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
                $this->prepareWhereBuilder($builder);
            });
        }

        return $this->prepareWhereBuilder($builder);
    }

    /**
     * Check whether the attribute contains a relation.
     *
     * @return bool
     */
    protected function hasRelation(): bool
    {
        if (! $this->mAttribute) {
            $this->setAttribute(new Attribute($this->attribute));
        }

        return $this->mAttribute->hasRelation();
    }

    /**
     * Proxy for Attribute::extractRelation.
     *
     * @return string
     */
    protected function extractRelation(): string
    {
        return $this->mAttribute->extractRelation();
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
        if (is_array($this->getParamValue())) {
            return $builder->where(function ($subBuilder) {
                $this->setBuilder($subBuilder)->buildForMultiValue();
            });
        }

        return $this->setBuilder($builder)->buildForSingleValue();
    }

    /**
     * Get the parameter's value.
     *
     * @return array|string
     */
    protected function getParamValue()
    {
        if (! $this->mParameter) {
            $this->setParameter(new Parameter($this->parameter, $this->isSearchable()));
        }

        return $this->mParameter->getValue();
    }

    /**
     * Check whether the operator is a LIKE operator.
     *
     * @return bool
     */
    protected function isSearchable(): bool
    {
        return strtolower($this->operator) == 'like';
    }

    /**
     * Iterate over the parameter value and append the
     * WHERE clauses.
     *
     * @return void
     */
    protected function buildForMultiValue(): void
    {
        collect($this->getParamValue())->each(function ($value, $index) {
            $index == 0
                ? $this->buildMultiValueClause($value)
                : $this->buildMultiValueClause($value, true);
        });
    }

    /**
     * Build query when parameter value is an array.
     *
     * @param $value
     * @param  bool  $forOr
     * @return Builder
     */
    protected function buildMultiValueClause($value, bool $forOr = false): Builder
    {
        return $forOr ? $this->buildOrWhereClause($value) : $this->buildWhereClause($value);
    }

    /**
     * Build orWhere query. If no $value is passed, normalized
     * value is used as default.
     *
     * @param  string|null  $value
     * @return Builder
     */
    protected function buildOrWhereClause(string $value = null): Builder
    {
        return $this->builder->orWhere($this->getAttrName(), $this->operator, $value ?: $this->getParamValue());
    }

    /**
     * Get the attribute's name.
     *
     * @return string
     */
    protected function getAttrName()
    {
        if (! $this->mAttribute) {
            $this->setAttribute(new Attribute($this->attribute));
        }

        return $this->mAttribute->getName();
    }

    /**
     * Build where query. If no $value is passed, normalized
     * value is used as default.
     *
     * @param  string|null  $value
     * @return Builder
     */
    protected function buildWhereClause(string $value = null): Builder
    {
        return $this->builder->where($this->getAttrName(), $this->operator, $value ?: $this->getParamValue());
    }

    /**
     * Build where query when paramValue is non-array value.
     *
     * @return Builder
     */
    protected function buildForSingleValue(): Builder
    {
        return $this->buildWhereClause();
    }

    /**
     * Proxy for Attribute::relationCount().
     *
     * @return int
     */
    protected function relationCount(): int
    {
        return $this->mAttribute->relationCount();
    }

    /**
     * Proxy for Attribute::extractAttribute().
     *
     * @return string
     */
    protected function extractAttribute(): string
    {
        return $this->mAttribute->extractAttribute();
    }
}

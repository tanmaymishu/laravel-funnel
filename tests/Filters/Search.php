<?php

namespace TanmayMishu\Tests\Filters;

use Illuminate\Database\Eloquent\Builder;
use TanmayMishu\LaravelFunnel\Filter;

class Search extends Filter
{
    /**
     * @var string
     */
    protected $parameter = 'title';

    /**
     * @var string
     */
    protected $attribute = 'title';

    /**
     * @var string
     */
    protected $operator = 'like';

    /**
     * Prepare the builder. Customize this according to your need.
     *
     * @param  Builder  $builder
     * @return Builder
     */
    protected function apply(Builder $builder): Builder
    {
        return $this->getDefaultWhereBuilder($builder);
    }
}

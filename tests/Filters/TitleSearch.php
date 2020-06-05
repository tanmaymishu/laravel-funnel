<?php

namespace TanmayMishu\Tests\Filters;

use Illuminate\Database\Eloquent\Builder;
use TanmayMishu\LaravelFunnel\Filter;

class TitleSearch extends Filter
{
    /**
     * @var string
     */
    protected $parameter = 'search';

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

<?php

namespace TanmayMishu\Tests\Filters;

use Illuminate\Database\Eloquent\Builder;
use TanmayMishu\LaravelFunnel\Filter;

class Published extends Filter
{
    /**
     * @var string
     */
    protected $parameter = 'published';

    /**
     * @var string
     */
    protected $attribute = 'is_published';

    /**
     * @var string
     */
    protected $operator = '=';

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

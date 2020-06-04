<?php

namespace TanmayMishu\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use TanmayMishu\LaravelFunnel\HasFilters;
use TanmayMishu\Tests\Filters\Published;
use TanmayMishu\Tests\Filters\Search;

class Post extends Model
{
    use HasFilters;

    protected $fillable = ['title', 'body', 'is_published'];

    protected $filters = [
        Published::class,
        Search::class,
    ];
}

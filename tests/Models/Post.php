<?php

namespace TanmayMishu\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use TanmayMishu\LaravelFunnel\HasFilters;
use TanmayMishu\Tests\Filters\Comment;
use TanmayMishu\Tests\Filters\Published;
use TanmayMishu\Tests\Filters\Reply;
use TanmayMishu\Tests\Filters\TitleMatch;
use TanmayMishu\Tests\Filters\TitleSearch;

class Post extends Model
{
    use HasFilters;

    protected $fillable = ['title', 'body', 'is_published'];

    protected $filters = [
        Published::class,
        TitleSearch::class,
        TitleMatch::class,
        Comment::class,
        Reply::class,
    ];

    public function comments()
    {
        return $this->hasMany(\TanmayMishu\Tests\Models\Comment::class);
    }
}

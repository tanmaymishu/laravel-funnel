<?php

namespace TanmayMishu\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['body'];

    public function replies()
    {
        return $this->hasMany(\TanmayMishu\Tests\Models\Reply::class);
    }
}

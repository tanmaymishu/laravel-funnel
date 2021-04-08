<?php

namespace TanmayMishu\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $fillable = ['body', 'post_id'];
}

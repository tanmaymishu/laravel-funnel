<?php

namespace TanmayMishu\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [FunnelTestServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__.'/migrations/create_posts_table.php.stub';
        include_once __DIR__.'/migrations/create_comments_table.php.stub';
        include_once __DIR__.'/migrations/create_replies_table.php.stub';

        (new \CreatePostsTable)->up();
        (new \CreateCommentsTable)->up();
        (new \CreateRepliesTable)->up();
    }
}

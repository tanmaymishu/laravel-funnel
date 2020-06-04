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

        (new \CreatePostsTable)->up();
    }
}

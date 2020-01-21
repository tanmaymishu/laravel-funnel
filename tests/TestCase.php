<?php

namespace TanmayMishu\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use TanmayMishu\LaravelFunnel\FunnelServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [FunnelServiceProvider::class];
    }
}

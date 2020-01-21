<?php

namespace TanmayMishu\Tests;

use Illuminate\Support\Facades\Artisan;

class FilterCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMockingConsoleOutput();
    }

    public function testAFilterCanBeCreated()
    {
        $this->artisan('funnel:filter A');
        $this->assertCommandCreatedOrExists();
    }

    public function testAFilterWithAttributeCanBeCreated()
    {
        $this->artisan('funnel:filter B -a foo');
        $this->assertCommandCreatedOrExists();
    }

    public function testAFilterWithParameterCanBeCreated()
    {
        $this->artisan('funnel:filter C -p foo');
        $this->assertCommandCreatedOrExists();
    }

    public function testAFilterWithOperatorCanBeCreated()
    {
        $this->artisan('funnel:filter D -o LIKE');
        $this->assertCommandCreatedOrExists();
    }

    public function testAFilterWithClauseCanBeCreated()
    {
        $this->artisan('funnel:filter E -c where');
        $this->assertCommandCreatedOrExists();
    }

    private function assertCommandCreatedOrExists(): void
    {
        $output = trim(Artisan::output());
        $this->assertTrue('Filter created successfully.' == $output || 'Filter already exists!' == $output);
    }


}

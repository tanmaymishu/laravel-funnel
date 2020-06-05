<?php

namespace TanmayMishu\Tests;

use TanmayMishu\LaravelFunnel\RequestParameter;

class RequestParameterTest extends TestCase
{
    public function testIfTheParamIsCommaDelimited()
    {
        $requestParam = new RequestParameter('lorem,ipsum,dolor');
        $this->assertTrue($requestParam->isCommaDelimited());

        $requestParam = new RequestParameter('loremipsumdolor');
        $this->assertFalse($requestParam->isCommaDelimited());
    }

    public function testIfTheParamIsMultiValue()
    {
        $requestParam = new RequestParameter('lorem,ipsum,dolor');
        $this->assertTrue($requestParam->isMultiValue());

        $requestParam = new RequestParameter(['lorem', 'ipsum', 'dolor']);
        $this->assertTrue($requestParam->isMultiValue());

        $requestParam = new RequestParameter(['lorem']);
        $this->assertTrue($requestParam->isMultiValue());

        $requestParam = new RequestParameter('loremipsumdolor');
        $this->assertFalse($requestParam->isMultiValue());
    }

    public function testMultiValueParamsCanBeConvertedToArray()
    {
        $requestParam = new RequestParameter('lorem,ipsum,dolor');
        $this->assertIsArray($requestParam->toArray());

        $requestParam = new RequestParameter(['lorem', 'ipsum', 'dolor']);
        $this->assertIsArray($requestParam->toArray());

        $requestParam = new RequestParameter('loremipsumdolor');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not convert to array. Param is neither an array, nor comma-delimited.');
        $this->assertIsArray($requestParam->toArray());
    }

    public function testSingleValueParamCanBeConvertedToLikeFriendlyArray()
    {
        $requestParam = new RequestParameter(['lorem', 'ipsum', 'dolor']);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not convert to like-friendly. Param is not a string.');
        $this->assertIsArray($requestParam->toLikeFriendly());
        $this->assertIsArray($requestParam->toLikeFriendly());

        $requestParam = new RequestParameter('loremipsumdolor');
        $this->assertEquals('%loremipsumdolor%', $requestParam->toLikeFriendly());
    }

}

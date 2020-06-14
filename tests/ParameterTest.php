<?php

namespace TanmayMishu\Tests;

use TanmayMishu\LaravelFunnel\Parameter;

class ParameterTest extends TestCase
{
    public function testIfTheParamIsCommaDelimited()
    {
        $requestParam = new Parameter('someName', true, 'lorem,ipsum,dolor');
        $this->assertTrue($requestParam->isCommaDelimited());

        $requestParam = new Parameter('someName', false, 'loremipsumdolor');
        $this->assertFalse($requestParam->isCommaDelimited());
    }

    public function testIfTheParamIsMultiValue()
    {
        $requestParam = new Parameter('someName', true, 'lorem,ipsum,dolor');
        $this->assertTrue($requestParam->isMultiValue());

        $requestParam = new Parameter('someName', true, ['lorem', 'ipsum', 'dolor']);
        $this->assertTrue($requestParam->isMultiValue());

        $requestParam = new Parameter('someName', true, ['lorem']);
        $this->assertTrue($requestParam->isMultiValue());

        $requestParam = new Parameter('someName', false, 'loremipsumdolor');
        $this->assertFalse($requestParam->isMultiValue());
    }

    public function testMultiValueParamsCanBeConvertedToArray()
    {
        $requestParam = new Parameter('someName', true, 'lorem,ipsum,dolor');
        $this->assertIsArray($requestParam->toArray());

        $requestParam = new Parameter('someName', true, ['lorem', 'ipsum', 'dolor']);
        $this->assertIsArray($requestParam->toArray());

        $requestParam = new Parameter('someName', false, 'loremipsumdolor');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not convert to array. Param is neither an array, nor comma-delimited.');
        $this->assertIsArray($requestParam->toArray());
    }

    public function testSingleValueParamCanBeConvertedToLikeFriendlyArray()
    {
        $requestParam = new Parameter('someName', true, ['lorem', 'ipsum', 'dolor']);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not convert to like-friendly. Param is not a string.');
        $this->assertIsArray($requestParam->toLikeFriendly());
        $this->assertIsArray($requestParam->toLikeFriendly());

        $requestParam = new Parameter('someName', false, 'loremipsumdolor');
        $this->assertEquals('%loremipsumdolor%', $requestParam->toLikeFriendly());
    }
}

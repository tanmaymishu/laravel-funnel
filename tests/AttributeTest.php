<?php

namespace TanmayMishu\Tests;

use TanmayMishu\LaravelFunnel\Attribute;

class AttributeTest extends TestCase
{
    public function testRelationExistenceInAnAttribute()
    {
        $attribute = new Attribute('body');
        $this->assertFalse($attribute->hasRelation());
        $attribute = new Attribute('comments.body');
        $this->assertTrue($attribute->hasRelation());
    }

    public function testRelationCountInAnAttribute()
    {
        $attribute = new Attribute('body');
        $this->assertEquals(0, $attribute->relationCount());
        $attribute = new Attribute('comments.body');
        $this->assertEquals(1, $attribute->relationCount());
    }

    public function testRelationCanBeExtractedFromAttribute()
    {
        $attribute = new Attribute('comments.body');
        $this->assertEquals('comments', $attribute->extractRelation());
    }

    public function testNestedRelationCanBeExtractedFromAttribute()
    {
        $attribute = new Attribute('comments.replies.body');
        $this->assertEquals('comments.replies', $attribute->extractRelation());
    }

    public function testAttributeCanBeExtractedFromRelationString()
    {
        $attribute = new Attribute('comments.replies.body');
        $this->assertEquals('body', $attribute->extractAttribute());
    }
}

<?php

namespace TanmayMishu\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use TanmayMishu\Tests\Models\Post;

class FilterHttpTest extends TestCase
{
    use WithFaker;

    public function testPublishedItemsCanBeMatched()
    {
        $this->getJson('/posts?published=1')
            ->assertJsonMissing(['is_published' => '0'])
            ->assertJson(['posts' => [$this->postA->toArray()]]);
    }

    public function testUnpublishedItemsCanBeMatched()
    {
        $this->getJson('/posts?published=0')
            ->assertJsonMissing(['is_published' => '1'])
            ->assertJson(['posts' => [$this->postB->toArray()]]);
    }

    public function testItemsCanBeMatched()
    {
        $this->getJson('/posts?title=lorem')
            ->assertJsonMissing(['title' => 'ipsum'])
            ->assertJson(['posts' => [$this->postA->toArray()]]);

        $this->getJson('/posts?title=ipsum')
            ->assertJsonMissing(['title' => 'lorem'])
            ->assertJson(['posts' => [$this->postB->toArray()]]);
    }

    public function testArrayParametersCanBeMatched()
    {
        $this->getJson('/posts?title[]=lorem&title[]=ipsum')
            ->assertJson(['posts' => [$this->postA->toArray(), $this->postB->toArray()]]);
    }

    public function testCommaSeparatedParametersCanBeMatched()
    {
        $this->getJson('/posts?title=lorem,ipsum')
            ->assertJson(['posts' => [$this->postA->toArray(), $this->postB->toArray()]]);
    }

    public function testParamValueMayContainCommaAndCanBeSearched()
    {
        $this->getJson('/posts?search=lorem, ipsum')
            ->assertJson(['posts' => [$this->postC->toArray()]]);
    }

    public function testFilterWithLikeOperatorAndArrayValuesCanBeSearched()
    {
        $this->getJson('/posts?search[]=lorem, ipsum&search[]=ipsum')
            ->assertJson(['posts' => [$this->postB->toArray(), $this->postC->toArray()]]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->postA = Post::create([
            'title' => 'lorem',
            'body'  => $this->faker->paragraph,
        ]);

        $this->postB = Post::create([
            'title'        => 'ipsum',
            'body'         => $this->faker->paragraph,
            'is_published' => 0,
        ]);

        $this->postC = Post::create([
            'title' => 'lorem, ipsum',
            'body'  => $this->faker->paragraph,
        ]);
    }
}

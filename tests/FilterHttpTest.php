<?php

namespace TanmayMishu\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use TanmayMishu\Tests\Models\Post;

class FilterHttpTest extends TestCase
{
    use WithFaker;

    public function testPublishedItemsCanBeFiltered()
    {
        $this->getJson('/posts?published=1')
            ->assertJsonMissing(['is_published' => '0'])
            ->assertJson(['posts' => [$this->postA->toArray()]]);
    }

    public function testUnpublishedItemsCanBeFiltered()
    {
        $this->getJson('/posts?published=0')
            ->assertJsonMissing(['is_published' => '1'])
            ->assertJson(['posts' => [$this->postB->toArray()]]);
    }

    public function testItemsCanBeSearched()
    {
        $this->getJson('/posts?title=lorem')
            ->assertJsonMissing(['title' => 'ipsum'])
            ->assertJson(['posts' => [$this->postA->toArray()]]);

        $this->getJson('/posts?title=ipsum')
            ->assertJsonMissing(['title' => 'lorem'])
            ->assertJson(['posts' => [$this->postB->toArray()]]);
    }

    public function testArrayParametersCanBeSearched()
    {
        $this->getJson('/posts?title[]=lorem&title[]=ipsum')
            ->assertJson(['posts' => [$this->postA->toArray(), $this->postB->toArray()]]);
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
    }
}

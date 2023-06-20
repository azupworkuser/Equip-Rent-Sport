<?php

namespace Tests\Unit\Model\Traits;

use App\Models\Traits\HasArchivedStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\TestCase;

// @codingStandardsIgnoreStart
class HasArchivedStatusTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            Schema::create('posts', static function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('status');
                $table->timestamps();
            });

            Post::factory()->count(10)->create();
        });

        parent::setUp();
    }

    public function test_it_can_get_archived()
    {
        Post::factory()->create(['status' => 'archived']);

        $this->assertCount(1, Post::archived()->get());
    }

    public function test_archived_post_is_not_in_default_query()
    {
        Post::factory()->create(['status' => 'archived']);

        $this->assertCount(10, Post::all());
    }

    public function test_with_archived_returns_all_posts()
    {
        Post::factory()->create(['status' => 'archived']);

        $this->assertCount(11, Post::withArchived()->get());
    }

    public function test_archived_returns_only_archived_posts()
    {
        Post::factory()->create(['status' => 'archived']);

        $this->assertCount(1, Post::archived()->get());
    }
}

class Post extends Model
{
    use HasFactory;
    use HasArchivedStatus;

    protected static function newFactory()
    {
        return PostFactory::new();
    }
}

class PostFactory extends Factory
{
    protected $model = Post::class;
    public function definition()
    {
        return [
            'title' => $this->faker->words(10, true),
            'status' => 'published'
        ];
    }
}

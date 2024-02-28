<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleModelTest extends FeatureTestCase
{
    use RefreshDatabase;

    private Author $author;

    public function setUp() :void
    {
        parent::setUp();
        $this->author = Author::factory()->create();     
    }

    /**
     * @test
     */
    public function 作成()
    {
        $model = Article::factory()->create(['author_id' => $this->author->id]);
        $this->assertInstanceOf(Article::class, $model);
        $this->assertDatabaseHas('articles', ['id' => $model->id]);
        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
    }

    /**
     * @test
     */
    public function 更新()
    {
        $model = Article::factory()->create(['author_id' => $this->author->id]);
        $model->update([
            'title' => 'Updated title',
            'content' => 'Updated content',
            'locale' => 'Updated locale',
            'llm_name' => 'Updated llm_name',
        ]);
        $this->assertEquals('Updated title', $model->title);
        $this->assertEquals('Updated content', $model->content);
        $this->assertEquals('Updated locale', $model->locale);
        $this->assertEquals('Updated llm_name', $model->llm_name);
    }

    /**
     * @test
     */
    public function 削除()
    {
        $model = Article::factory()->create(['author_id' => $this->author->id]);
        $model->forceDelete();
        $this->assertModelMissing($model);
    }

        /**
     * @test
     */
    public function ソフトデリート()
    {
        $model = Article::factory()->create(['author_id' => $this->author->id]);
        $model->delete();
        $this->assertSoftDeleted($model);
    }

    /**
     * @test
     */
    public function リストア()
    {
        $model = Article::factory()->create(['author_id' => $this->author->id]);
        $model->delete();
        $model->restore();
        $this->assertDatabaseHas('articles', ['id' => $model->id]);
    }

    /**
     * @test
     */
    public function author()
    {
        $model = Article::factory()->create(['author_id' => $this->author->id]);
        $this->assertInstanceOf(Article::class, $model->author->articles->first());
    }

    /**
     * @test
     */
    public function image()
    {
        $model = Article::factory()->create(['author_id' => $this->author->id]);
        $image = Image::factory()->create(['article_id' => $model->id]);
        $this->assertEquals($image->id, $model->image->id);
    }

}

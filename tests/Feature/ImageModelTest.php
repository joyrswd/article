<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Article;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageModelTest extends FeatureTestCase
{
    use RefreshDatabase;

    private Article $article;

    public function setUp():void
    {
        parent::setUp();
        $author = Author::factory()->create();
        $this->article = Article::factory()->create(['author_id' => $author->id]);
    }

    /**
     * @test
     */
    public function 作成()
    {
        $model = Image::factory()->create(['article_id' => $this->article->id]);
        $this->assertInstanceOf(Image::class, $model);
        $this->assertDatabaseHas('images', ['id' => $model->id]);
        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
    }

    /**
     * @test
     */
    public function 更新()
    {
        $model = Image::factory()->create(['article_id' => $this->article->id]);
        $model->update([
            'path' => 'Updated path',
            'description' => 'Updated description',
            'size' => 'Updated size',
            'model_name' => 'Updated model_name',
        ]);
        $this->assertEquals('Updated path', $model->path);
        $this->assertEquals('Updated description', $model->description);
        $this->assertEquals('Updated size', $model->size);
        $this->assertEquals('Updated model_name', $model->model_name);
    }

    /**
     * @test
     */
    public function 削除()
    {
        $model = Image::factory()->create(['article_id' => $this->article->id]);
        $model->delete();
        $this->assertModelMissing($model);
    }

    /**
     * @test
     */
    public function article()
    {
        $model = Image::factory()->create(['article_id' => $this->article->id]);
        $this->assertEquals($this->article->id, $model->article->id);
    }

}

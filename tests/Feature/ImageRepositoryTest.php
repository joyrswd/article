<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Article;
use App\Models\Image;
use App\Repositories\ImageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageRepositoryTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected Image $model;
    private ImageRepository $repository;

    public function setUp() :void
    {
        parent::setUp();
        $author = Author::factory()->create();
        $article = Article::factory()->create(['author_id' => $author->id]);
        $this->model = Image::factory()->create(['article_id' => $article]);
        $this->repository = app(ImageRepository::class);
    }

    /**
     * @test
     */
    public function read_正常() : void
    {
        $result = $this->repository->read($this->model->id);
        $this->assertEquals($this->model->id, $result['id']);
    }

    /**
     * @test
     */
    public function create_正常() : void
    {
        $article = Article::factory()->create(['author_id' => $this->model->article->author->id]);
        $id = $this->repository->create([
            'article_id' => $article->id,
            'path' => 'new path',
            'description' => 'new description',
            'size' => 'new size',
            'model_name' => 'new model_name',
        ]);
        $result = $this->repository->read($id);
        $this->assertEquals('new path', $result['path']);
        $this->assertEquals('new description', $result['description']);
        $this->assertEquals('new size', $result['size']);
        $this->assertEquals('new model_name', $result['model_name']);
    }

    /**
     * @test
     */
    public function update_正常() : void
    {
        $this->repository->update($this->model->id, [
            'path' => 'update path',
            'description' => 'update description',
            'size' => 'update size',
            'model_name' => 'update model_name',
        ]);
        $result = $this->repository->read($this->model->id);
        $this->assertEquals('update path', $result['path']);
        $this->assertEquals('update description', $result['description']);
        $this->assertEquals('update size', $result['size']);
        $this->assertEquals('update model_name', $result['model_name']);
    }

    /**
     * @test
     */
    public function delete_正常() : void
    {
        $this->repository->delete($this->model->id);
        $result = $this->repository->read($this->model->id);
        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function find_正常() : void
    {
        $result = $this->repository->find([
            'path' => $this->model->path
        ]);
        $flag = false;
        foreach ($result as $record) {
            if ($record['id'] === $this->model->id) {
                $flag = true;
                break;
            }
        }
        $this->assertTrue($flag);
    }

}

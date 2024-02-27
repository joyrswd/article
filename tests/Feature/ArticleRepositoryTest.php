<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Image;
use App\Repositories\ArticleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleRepositoryTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected Article $model;
    private ArticleRepository $repository;
    private Author $parent;

    public function setUp() :void
    {
        parent::setUp();
        $this->parent = Author::factory()->create();
        $this->model = Article::factory()->create(['author_id' => $this->parent->id]);
        $this->repository = app(ArticleRepository::class);
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
        $id = $this->repository->create([
            'author_id' => $this->parent->id,
            'title' => '新規タイトル',
            'content' => '新規本文',
            'locale' => 'ja',
            'llm_name' => 'ai',
        ]);
        $result = $this->repository->read($id);
        $this->assertEquals('新規タイトル', $result['title']);
        $this->assertEquals('新規本文', $result['content']);
        $this->assertEquals('ja', $result['locale']);
        $this->assertEquals('ai', $result['llm_name']);
    }

    /**
     * @test
     */
    public function update_正常() : void
    {
        $newData = $this->model->name . '更新';
        $this->repository->update($this->model->id, [
            'title' => $newData,
        ]);
        $result = $this->repository->read($this->model->id);
        $this->assertEquals($newData, $result['title']);
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
    public function restore_正常() : void
    {
        $this->repository->delete($this->model->id);
        $this->repository->restore($this->model->id);
        $result = $this->repository->read($this->model->id);
        $this->assertEquals($this->model->id, $result['id']);
    }

    /**
     * @test
     */
    public function forceDelete_正常() : void
    {
        $this->repository->forceDelete($this->model->id);
        $result = $this->repository->read($this->model->id);
        $this->assertEmpty($result);
        $this->repository->restore($this->model->id);
        $result = $this->repository->read($this->model->id);
        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function find_正常() : void
    {
        $newModel = Article::factory()->create([
            'author_id' => $this->parent->id,
        ]);
        $result = $this->repository->find([
            'title' => $newModel->title
        ]);
        $flag = false;
        foreach ($result as $record) {
            if ($record['id'] === $newModel->id) {
                $flag = true;
                break;
            }
        }
        $this->assertTrue($flag);
    }

    /**
     * @test
     */
    public function create_with_image_正常() : void
    {
        $id = $this->repository->create([
            'author_id' => $this->parent->id,
            'title' => '新規タイトル',
            'content' => '新規本文',
            'locale' => 'ja',
            'llm_name' => 'ai',
            'image' => [
                'path' => 'new path',
                'description' => 'new description',
                'size' => 'new size',
                'model_name' => 'new model_name',
            ]
        ]);
        $result = $this->repository->read($id);
        $this->assertEquals('new path', $result['image']['path']);
        $this->assertEquals('new description', $result['image']['description']);
        $this->assertEquals('new size', $result['image']['size']);
        $this->assertEquals('new model_name', $result['image']['model_name']);
    }

    /**
     * @test
     */
    public function update_with_image_正常() : void
    {
        $image = Image::factory()->create(['article_id' => $this->model->id, 'path' => 'firstPath']);
        $this->assertEquals('firstPath', $this->model->image->path);
        $this->repository->update($this->model->id, [
            'title' => '新規タイトル',
            'image' => [ ['id' => $image->id],
                        ['path' => 'Updated path']
            ]
        ]);
        $result = $this->repository->read($this->model->id);
        $this->assertEquals('新規タイトル', $result['title']);
        $this->assertEquals($image->id, $result['image']['id']);
        $this->assertEquals('Updated path', $result['image']['path']);
    }

}

<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttributeRepositoryTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected Attribute $model;
    private AttributeRepository $repository;

    public function setUp() :void
    {
        parent::setUp();
        $this->model = Attribute::factory()->create();
        $this->repository = app(AttributeRepository::class);
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
            'name' => '新規データ',
            'type' => '新規タイプ',
        ]);
        $result = $this->repository->read($id);
        $this->assertEquals('新規データ', $result['name']);
        $this->assertEquals('新規タイプ', $result['type']);
    }

    /**
     * @test
     */
    public function update_正常() : void
    {
        $newData = $this->model->name . '更新';
        $this->repository->update($this->model->id, [
            'name' => $newData,
        ]);
        $result = $this->repository->read($this->model->id);
        $this->assertEquals($newData, $result['name']);
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
        $newModel = Attribute::factory()->create();
        $result = $this->repository->find([
            'name' => $newModel->name
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
    public function create_with_author_正常() : void
    {
        $authors = [
            Author::factory()->create(),
            Author::factory()->create(),
        ];
        $id = $this->repository->create([
            'name' => 'aaa',
            'type' => 'bbb',
            'authors' => $authors
        ]);
        $result = $this->repository->read($id);
        $this->assertEquals($authors[0]->id, $result['authors'][0]['id']);
        $this->assertEquals($authors[1]->id, $result['authors'][1]['id']);
    }

}

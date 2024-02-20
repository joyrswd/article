<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Attribute;
use App\Repositories\AuthorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorRepositoryTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected Author $model;
    private AuthorRepository $repository;

    public function setUp() :void
    {
        parent::setUp();
        $this->model = Author::factory()->create();
        $this->repository = app(AuthorRepository::class);
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
        ]);
        $result = $this->repository->read($id);
        $this->assertEquals('新規データ', $result['name']);
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
        $newModel = Author::factory()->create();
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
    public function create_with_attributes_正常() : void
    {
        $attributes = [
            Attribute::factory()->create(),
            Attribute::factory()->create(),
        ];
        $id = $this->repository->create([
            'name' => 'aaa',
            'attributes' => $attributes
        ]);
        $result = $this->repository->read($id);
        $this->assertEquals($attributes[0]->id, $result['attributes'][0]['id']);
        $this->assertEquals($attributes[1]->id, $result['attributes'][1]['id']);
    }

}

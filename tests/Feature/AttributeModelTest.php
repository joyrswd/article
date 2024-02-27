<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttributeModelTest extends FeatureTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function 作成()
    {
        $model = Attribute::factory()->create();
        $this->assertInstanceOf(Attribute::class, $model);
        $this->assertDatabaseHas('attributes', ['id' => $model->id]);
        $this->assertNotNull($model->created_at);
        $this->assertNotNull($model->updated_at);
    }

    /**
     * @test
     */
    public function 更新()
    {
        $model = Attribute::factory()->create();
        $model->update([
            'name' => 'Updated Name',
            'type' => 'Updated Type',
        ]);
        $this->assertEquals('Updated Name', $model->name);
        $this->assertEquals('Updated Type', $model->type);
    }

    /**
     * @test
     */
    public function 削除()
    {
        $model = Attribute::factory()->create();
        $model->delete();
        $this->assertModelMissing($model);
    }

    /**
     * @test
     */
    public function authors()
    {
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();
        $model = Attribute::factory()->create();
        $model->authors()->attach([$author1->id, $author2->id]);
        $this->assertCount(2, $model->authors);
        $this->assertInstanceOf(Attribute::class, $model->authors->first()->attributes->first());
    }

}

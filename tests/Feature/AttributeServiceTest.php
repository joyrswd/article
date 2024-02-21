<?php

namespace Tests\Feature;

use App\Services\AttributeService;
use App\Models\Attribute;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttributeServiceTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected Attribute $model;
    private AttributeService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(AttributeService::class);
    }

    /**
     * @test
     */
    public function addOrFind_追加_正常(): void
    {
        $this->assertDatabaseMissing('attributes', ['name' => 'attr1', 'type' => 'key1']);
        $this->assertDatabaseMissing('attributes', ['name' => 'attr2', 'type' => 'key2']);
        $result = $this->service->addOrFind([
            'key1' => 'attr1',
            'key2' => 'attr2',
        ]);
        $this->assertDatabaseHas('attributes', ['name' => 'attr1', 'type' => 'key1']);
        $this->assertDatabaseHas('attributes', ['name' => 'attr2', 'type' => 'key2']);
        $this->assertEquals('attr1', $result[0]['name']);
        $this->assertEquals('key1', $result[0]['type']);
        $this->assertEquals('attr2', $result[1]['name']);
        $this->assertEquals('key2', $result[1]['type']);
    }

    /**
     * @test
     */
    public function addOrFind_既存_正常(): void
    {
        $attr1 = Attribute::factory()->create();
        $attr2 = Attribute::factory()->create();
        $this->assertDatabaseHas('attributes', ['id' => $attr1->id]);
        $this->assertDatabaseHas('attributes', ['id' => $attr2->id]);
        $result = $this->service->addOrFind([
            $attr1->type => $attr1->name,
            $attr2->type => $attr2->name,
        ]);
        $this->assertEquals($attr1->name, $result[0]['name']);
        $this->assertEquals($attr1->type, $result[0]['type']);
        $this->assertEquals($attr2->name, $result[1]['name']);
        $this->assertEquals($attr2->type, $result[1]['type']);
    }

    /**
     * @test
     */
    public function addOrFind_追加_既存_正常(): void
    {
        $attr1 = Attribute::factory()->create();
        $this->assertDatabaseHas('attributes', ['id' => $attr1->id]);
        $this->assertDatabaseMissing('attributes', ['name' => 'attr2', 'type' => 'key2']);
        $result = $this->service->addOrFind([
            $attr1->type => $attr1->name,
            'key2' => 'attr2',
        ]);
        $this->assertDatabaseHas('attributes', ['name' => 'attr2', 'type' => 'key2']);
        $this->assertEquals($attr1->name, $result[0]['name']);
        $this->assertEquals($attr1->type, $result[0]['type']);
        $this->assertEquals('attr2', $result[1]['name']);
        $this->assertEquals('key2', $result[1]['type']);
    }

}

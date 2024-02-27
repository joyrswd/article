<?php

namespace Tests\Feature;

use App\Services\ImageService;
use App\Models\Image;
use App\Models\Article;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use finfo;
use Mockery;

class ImageServiceTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected Image $model;
    private ImageService $service;
    private string $filepath = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(ImageService::class);
        $this->setPrivateProperty('dirs', ['tmp', 'test', '20240101'], $this->service);
        $this->filepath = public_path() . '/tmp/test/20240101';
    }

    protected function tearDown(): void
    {
        $path = $this->filepath;
        while(is_dir($path)) {
            rmdir($path);
            $path = dirname($path);
            if ($path === public_path())
            {
                break;
            }
        }
        parent::tearDown();
    }

    /**
     * @test
     */
    public function setUpDirectory_正常(): void
    {
        $this->callPrivateMethod('setUpDirectory', $this->service);
        $this->assertDirectoryExists($this->filepath);
    }

    /**
     * @test
     */
    public function put_正常(): void
    {
        Http::shouldReceive('withoutVerifying')->andReturn(new class{
            public function get($url) {
                return new class {
                    public function successful(){return true;}
                    public function body(){return 'imageBody';}
                };
            }
        });
        $finfoMock = Mockery::mock(finfo::class);
        $finfoMock->shouldReceive('buffer')->once()->andReturn('png');
        $this->setPrivateProperty('finfo', $finfoMock, $this->service);
        $path = $this->service->put('http://dummy.test');
        $this->assertFileExists($path);
        unlink($path);
    }

    /**
     * @test
     */
    public function add_正常(): void
    {
        $article = Article::factory()->create(['author_id' => Author::factory()->create()->id]);
        $this->service->add($article->id, 'path', 'description', 'size', 'model_name');
        $this->assertDatabaseHas('images', [
            'article_id' => $article->id,
            'path' => 'path',
            'description' => 'description',
            'size' => 'size',
            'model_name' => 'model_name',
        ]);
    }

}

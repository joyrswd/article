<?php

namespace Tests\Feature;

use App\Services\ImageService;
use App\Models\Image;
use App\Models\Article;
use App\Models\Author;
use App\Repositories\ImagickRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
            //rmdir($path);
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

    /**
     * @test
     */
    public function put_正常(): void
    {
        $mock = mock(ImagickRepository::class);
        $mock->shouldReceive('setRectImage')->once()->andReturn(1);
        $mock->shouldReceive('setTextOnImage')->once();
        $mock->shouldReceive('setImageByUrl')->once()->andReturn(2);
        $mock->shouldReceive('minimize')->once();
        $mock->shouldReceive('compositeOver')->once();
        $mock->shouldReceive('save')->once();
        $mock->shouldReceive('clear')->once();
        $this->setPrivateProperty('imagickRepository', $mock, $this->service);
        $url = 'http://test.com/example.png';
        $path = $this->service->put($url, 'watermark');
        $expected = '/var/www/article/public/tmp/test/20240101/' . md5($url) . '.png';
        $this->assertEquals($expected, $path);
    }


}

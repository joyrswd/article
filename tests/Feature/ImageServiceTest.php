<?php

namespace Tests\Feature;

use App\Services\ImageService;
use App\Models\Image;
use App\Models\Article;
use App\Models\Author;
use App\Repositories\ImagickRepository;
use DateTime;
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
    public function prepareImagePath_正常(): void
    {
        $path = $this->callPrivateMethod('prepareImagePath', $this->service, '画像');
        $expect = public_path() . '/tmp/test/20240101/' . md5('画像') . '.png';
        $this->assertEquals($expect, $path);
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function setUpWatermark_正常(): void
    {
        $this->callPrivateMethod('setUpWatermark', $this->service, 'ウォーターマーク');
    }

    /**
     * @test
     */
    public function add_正常(): void
    {
        $article = Article::factory()->create(['author_id' => Author::factory()->create()->id]);
        $this->service->add($article->id, 'path', 'model_name');
        $this->assertDatabaseHas('images', [
            'article_id' => $article->id,
            'path' => 'path',
            'model_name' => 'model_name',
        ]);
    }

    /**
     * @test
     */
    public function put_正常(): void
    {
        $mock = mock(app(ImagickRepository::class))->makePartial();
        $mock->shouldReceive('setBinaryImage')->once();
        $mock->shouldReceive('minimize')->once();
        $mock->shouldReceive('compositeOver')->once();
        $mock->shouldReceive('save')->once();
        $mock->shouldReceive('clear')->once();
        $this->setPrivateProperty('imagickRepository', $mock, $this->service);
        $path = $this->service->put('binary', 'watermark');
        $expected = public_path() . '/tmp/test/20240101/' . md5('binary') . '.png';
        $this->assertEquals($expected, $path);
    }

    /**
     * @test
     */
    public function findByPage_正常(): void
    {
        $auhtor = Author::factory()->create();
        $items = [];
        while (count($items) < 3) {
            $date = '2021-01-0' . (count($items)+1);
            $article = Article::factory()->create(['author_id' => $auhtor, 'locale' => app()->currentLocale()]);
            $items[] = Image::factory()->create(['article_id' => $article, 'created_at' => new DateTime($date)]);
        }
        $result = $this->service->findByPage(2, []);
        $this->assertEquals($items[count($items)-1]->id, $result['data'][0]['id']);
        $this->assertEquals($items[count($items)-2]->id, $result['data'][1]['id']);
        $ids = array_column($result['data'], 'id');
        $this->assertNotContains($items[count($items)-3]->id, $ids);
    }

    /**
     * @test
     */
    public function get_正常(): void
    {
        $auhtor = Author::factory()->create();
        $article = Article::factory()->create(['author_id' => $auhtor, 'locale' => app()->currentLocale()]);
        $image = Image::factory()->create(['article_id' => $article]);
        $result = $this->service->get($image->id);
        $this->assertEquals($image->id, $result['id']);
    }

}

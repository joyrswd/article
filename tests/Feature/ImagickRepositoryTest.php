<?php

namespace Tests\Feature;

use App\Repositories\ImagickRepository;
use Imagick;
use ImagickDraw;

class ImagickRepositoryTest extends FeatureTestCase
{

    private ImagickRepository $repository;

    public function setUp():void
    {
        parent::setUp();
        $imagick = mock(Imagick::class)->makePartial();
        $imagick->shouldReceive('readImage');
        $imagick->shouldReceive('annotateImage');
        $draw = mock(ImagickDraw::class);
        $this->repository = new ImagickRepository($imagick, $draw);
    }

    /**
     * @test
     */
    public function setImager_正常(): void
    {
        $array = $this->getPrivateProperty('instances', $this->repository);
        $this->assertCount(0, $array);
        $index = $this->callPrivateMethod('setImager', $this->repository);
        $this->assertEquals(0, $index);
        $array = $this->getPrivateProperty('instances', $this->repository);
        $this->assertCount(1, $array);
        $this->assertInstanceOf(Imagick::class, $array[$index]);
    }

    /**
     * @test
     */
    public function setDrawer_正常(): void
    {
        $array = $this->getPrivateProperty('instances', $this->repository);
        $this->assertCount(0, $array);
        $index = $this->callPrivateMethod('setDrawer', $this->repository);
        $this->assertEquals(0, $index);
        $array = $this->getPrivateProperty('instances', $this->repository);
        $this->assertCount(1, $array);
        $this->assertInstanceOf(ImagickDraw::class, $array[$index]);
    }

    /**
     * @test
     */
    public function getInstance_正常(): void
    {
        $index = $this->callPrivateMethod('setImager', $this->repository);
        $result = $this->callPrivateMethod('getInstance', $this->repository, $index);
        $this->assertInstanceOf(Imagick::class, $result);
    }

    /**
     * @test
     */
    public function getInstance_異常(): void
    {
        $index = $this->callPrivateMethod('setImager', $this->repository);
        $this->expectException(\Exception::class);
        $this->callPrivateMethod('getInstance', $this->repository, $index+1);
    }

    /**
     * @test
     */
    public function setImageByUrl_正常(): void
    {
        $result = $this->repository->setImageByUrl('http://text.com/example.png');
        $this->assertEquals(0, $result);
        $result = $this->callPrivateMethod('getInstance', $this->repository, $result);
        $this->assertInstanceOf(Imagick::class, $result);
    }

    /**
     * @test
     */
    public function setRectImage_正常(): void
    {
        $result = $this->repository->setRectImage(50, 100, 'white', 'png');
        $this->assertEquals(0, $result);
        $result = $this->callPrivateMethod('getInstance', $this->repository, $result);
        $this->assertInstanceOf(Imagick::class, $result);
        $this->assertEquals(50, $result->getImageWidth());
        $this->assertEquals(100, $result->getImageHeight());
    }

    /**
     * @test
     */
    public function setTextOnImage_正常(): void
    {
        $id = $this->repository->setRectImage(100, 100, 'white', 'png');
        $this->repository->setTextOnImage($id, 'テキスト', []);
        $result = $this->callPrivateMethod('getInstance', $this->repository, $id+1);
        $this->assertInstanceOf(ImagickDraw::class, $result);
    }

    /**
     * @test
     */
    public function minimize_正常(): void
    {
        $id = $this->repository->setRectImage(150, 200, 'white', 'png');
        $result = $this->callPrivateMethod('getInstance', $this->repository, $id);
        $this->assertEquals(150, $result->getImageWidth());
        $this->assertEquals(200, $result->getImageHeight());
        $this->repository->minimize($id, 50, 100);
        $result = $this->callPrivateMethod('getInstance', $this->repository, $id);
        $this->assertEquals(50, $result->getImageWidth());
        $this->assertEquals(100, $result->getImageHeight());
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function compositeOver_正常() :void
    {
        $id1 = $this->repository->setRectImage(100, 100, 'white', 'png');
        $id2 = $this->repository->setRectImage(10, 10, 'black', 'png');
        $this->repository->compositeOver($id1, $id2);
    }

    /**
     * @test
     */
    public function save_正常() :void
    {
        $id = $this->repository->setRectImage(234, 100, 'white', 'png');
        $tempfile = sys_get_temp_dir() . '/test.png';
        $this->repository->save($id, $tempfile);
        $this->assertFileExists($tempfile);
        $image = new Imagick($tempfile);
        $this->assertEquals(234, $image->getImageWidth());
        unlink($tempfile);
    }

    /**
     * @test
     */
    public function clear_正常() :void
    {
        $id = $this->repository->setRectImage(100, 100, 'white', 'png');
        $instance = $this->callPrivateMethod('getInstance', $this->repository, $id);
        $this->assertInstanceOf(Imagick::class, $instance);
        $this->repository->clear($id);
        $this->expectException(\Exception::class);
        $instance = $this->callPrivateMethod('getInstance', $this->repository, $id);
    }

    /**
     * @test
     */
    public function clear_all_正常() :void
    {
        $id1 = $this->repository->setRectImage(100, 100, 'white', 'png');
        $id2 = $this->repository->setRectImage(100, 100, 'white', 'png');
        $instances = $this->getPrivateProperty('instances', $this->repository);
        $this->assertInstanceOf(Imagick::class, $instances[$id1]);
        $this->assertInstanceOf(Imagick::class, $instances[$id2]);
        $this->repository->clear();
        $instances = $this->getPrivateProperty('instances', $this->repository);
        $this->assertNull($instances[$id1]);
        $this->assertNull($instances[$id2]);
    }

}

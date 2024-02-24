<?php

namespace Tests\Feature;

use App\Services\RssService;

class RssServiceTest extends FeatureTestCase
{

    private RssService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(RssService::class);
    }

    /**
     * @test
     */
    public function getFilePath_正常(): void
    {
        $path = $this->callPrivateMethod('getFilePath', $this->service);
        $this->assertFileExists($path);
    }

    /**
     * @test
     */
    public function fetchRss_正常(): void
    {
        $articles = [];
        for ($i=0; $i < 10; $i++) {
            $articles[$i] = [
                'id' => $i,
                'title' => 'タイトル' . $i,
                'content' => '本文' . $i,
                'created_at' => now(),
                'llm_name' => 'test',
            ];
        }
        $path = $this->getPrivateProperty('path', $this->service);
        $path .= '.test';
        $this->setPrivateProperty('path', $path, $this->service);

        $this->service->fetchRss($articles);
        $this->assertFileExists($path);
        $xml = simplexml_load_file($path);
        $this->assertNotFalse($xml);
        $i = 0;
        foreach ($xml->channel->item as $item) {
            $this->assertEquals($articles[$i++]['title'], $item->title);
        }
        unlink($path);
    }
}

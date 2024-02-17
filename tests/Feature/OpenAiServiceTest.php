<?php

namespace Tests\Feature;

use App\Services\OpenAiService;

class OpenAiServiceTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function makeAuthor_正常(): void
    {
        $result = $this->callPrivate('makeAuthor', OpenAiService::class);
        $this->assertStringMatchesFormat('あなたは%sに詳しい%sな%sです。', $result);
    }

    /**
     * @test
     */
    public function makePost_正常(): void
    {
        $class = app(OpenAiService::class);
        $date = new \DateTime();
        $result = $class->makePost($date);
        $this->assertIsArray($result);
        dd($result);
    }

}

<?php

namespace Tests\Feature;

use App\Repositories\GoogleAiRepository;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Assert;

class GoogleAiRepositoryTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function setMessage_正常(): void
    {
        $repository = new GoogleAiRepository('secret', 'endpoint', 'model', 60);
        $repository->setMessage('テスト', 'user');
        $messages = $this->getPrivateProperty('messages', $repository);
        $this->assertEquals([['text' => 'テスト']], $messages);
    }

    /**
     * @test
     */
    public function setMessage_複数_正常(): void
    {
        $repository = new GoogleAiRepository('secret', 'endpoint', 'model', 60);
        $repository->setMessage('テスト1', 'system');
        $repository->setMessage('テスト2', 'system');
        $messages = $this->getPrivateProperty('messages', $repository);
        $this->assertEquals([['text' => 'テスト1'], ['text' => 'テスト2']], $messages);
    }

    /**
     * @test
     */
    public function excute_正常(): void
    {

        Http::fake();
        Http::shouldReceive('withHeaders')
            ->once()
            ->andReturn(new class extends Assert {
                public function timeout($timeout) {
                    $this->assertEquals(60, $timeout);
                    return new class extends Assert {
                        public function post ($endpoint, $params) {
                            $this->assertEquals('endpoint', $endpoint);
                            $this->assertEquals('secret', $params['secret']);
                            $this->assertEquals('model', $params['model']);
                            return new class {
                                public function json() {
                                    return [];
                                }
                            };
                        }
                    };
                }
            });
        $repository = new GoogleAiRepository('secret', 'endpoint', 'model', 60);
        $repository->setMessage('テスト', 'system');
        $result = $repository->excute();
        $this->assertIsArray($result);
    }

}

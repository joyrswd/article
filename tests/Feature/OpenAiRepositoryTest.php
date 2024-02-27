<?php

namespace Tests\Feature;

use App\Repositories\OpenAiRepository;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Assert;

class OpenAiRepositoryTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function setMessage_正常(): void
    {
        $repository = new OpenAiRepository('secret', 'endpoint', 'model', 60);
        $repository->setMessage('テスト', 'user');
        $messages = $this->getPrivateProperty('messages', $repository);
        $this->assertEquals([['role' => 'user', 'content' => 'テスト']], $messages);
    }

    /**
     * @test
     */
    public function setMessage_system_正常(): void
    {
        $repository = new OpenAiRepository('secret', 'endpoint', 'model', 60);
        $repository->setMessage('テストsystem', 'system');
        $messages = $this->getPrivateProperty('messages', $repository);
        $this->assertEquals([['role' => 'system', 'content' => 'テストsystem']], $messages);
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
        $repository = new OpenAiRepository('secret', 'endpoint', 'model', 60);
        $repository->setMessage('テスト', 'system');
        $result = $repository->makeText();
        $this->assertIsArray($result);
    }



}

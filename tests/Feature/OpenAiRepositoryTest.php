<?php

namespace Tests\Feature;

use App\Repositories\OpenAiRepository;
use App\Enums\OpenAiRoleEnum;
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
        $repository->setMessage('テスト');
        $messages = $this->getPrivateProperty('messages', $repository);
        $this->assertEquals([['role' => OpenAiRoleEnum::User, 'content' => 'テスト']], $messages);
    }

    /**
     * @test
     */
    public function setMessage_system_正常(): void
    {
        $repository = new OpenAiRepository('secret', 'endpoint', 'model', 60);
        $repository->setMessage('テストsystem', OpenAiRoleEnum::System);
        $messages = $this->getPrivateProperty('messages', $repository);
        $this->assertEquals([['role' => OpenAiRoleEnum::System, 'content' => 'テストsystem']], $messages);
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
        $repository->setMessage('テスト');
        $result = $repository->excute();
        $this->assertIsArray($result);
    }



}

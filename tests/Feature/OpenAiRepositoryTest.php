<?php

namespace Tests\Feature;

use App\Repositories\OpenAiRepository;
use Illuminate\Support\Facades\Http;

class OpenAiRepositoryTest extends FeatureTestCase
{
    /**
     * @test
     */
    public function setMessage_正常(): void
    {
        $repository = new OpenAiRepository();
        $repository->setMessage('テスト', 'user');
        $messages = $this->getPrivateProperty('messages', $repository);
        $this->assertEquals([['role' => 'user', 'content' => 'テスト']], $messages);
    }

    /**
     * @test
     */
    public function setMessage_system_正常(): void
    {
        $repository = new OpenAiRepository();
        $repository->setMessage('テストsystem', 'system');
        $messages = $this->getPrivateProperty('messages', $repository);
        $this->assertEquals([['role' => 'system', 'content' => 'テストsystem']], $messages);
    }

    /**
     * @test
     */
    public function makeText_正常(): void
    {
        Http::fake();
        Http::shouldReceive('withHeaders')
            ->once()->andReturn(new class {
                public function timeout() {
                    return new class {
                        public function post () {
                            return new class {
                                public function json() {return [];}
                            };
                        }
                    };
                }
            });
        $repository = new OpenAiRepository();
        $repository->setMessage('テスト', 'system');
        $result = $repository->makeText();
        $this->assertIsArray($result);
    }

}

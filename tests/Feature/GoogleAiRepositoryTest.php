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
        $repository = new GoogleAiRepository();
        $repository->setMessage('テスト', 'system');
        $result = $repository->makeText();
        $this->assertIsArray($result);
    }

}

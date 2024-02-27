<?php

namespace Tests\Feature;

use App\Repositories\OpenAiRepository;
use GuzzleHttp\Psr7\Message;
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
    public function makeText_正常(): void
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
        $repository = new OpenAiRepository();
        $repository->setMessage('テスト', 'system');
        $result = $repository->makeText();
        $this->assertIsArray($result);
    }

    /**
     * @test
     */
    public function makeImage_正常(): void
    {
        $message = <<<MESSAGE
        As the winter season continues, many animals are facing challenges in finding food and staying warm. It is important for us to be mindful of their needs during this time of year.

        One way we can help is by setting up bird feeders in our yards or gardens. By providing a source of food for birds, we can help them survive the cold winter months when natural food sources may be scarce. Make sure to keep the feeders stocked with seeds, nuts, and suet to attract a variety of bird species.
        
        Additionally, if you have pets that spend time outdoors, it's essential to take extra precautions in the cold weather. Make sure they have access to shelter that is insulated and protected from the wind. Provide them with plenty of fresh water, as it can freeze quickly in low temperatures.
        
        For wildlife enthusiasts, February is a great time to observe animal behavior in the wild. Many animals are preparing for the upcoming breeding season, which can lead to interesting displays of courtship and territorial behavior. Keep an eye out for mating rituals among birds, mammals, and even insects.
        
        If you're looking to get involved in conservation efforts, consider volunteering at a local wildlife rehabilitation center. These facilities often see an increase in injured or orphaned animals during the winter months and rely on volunteers to help care for them.
        
        Remember, even small actions can make a big difference in the lives of animals during the challenging winter season. Whether it's setting up a bird feeder, providing shelter for outdoor pets, or volunteering at a wildlife rehabilitation center, there are plenty of ways to show your love and support for our furry and feathered friends.
MESSAGE;
        $command = "次の文章の挿絵になる画像を生成してください。\n" . $message;
        $repository = new OpenAiRepository();
        $response = $repository->makeImage($command);
        dd($response);
    }


}

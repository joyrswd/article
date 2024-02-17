<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OpenAiRepository;
use App\Enums\OpenAiRoleEnum;
use App\Enums\AiGenreEnum;
use App\Enums\AiAdjectiveEnum;
use App\Enums\AiPersonalityEnum;
use App\Enums\AiGenerationEnum;
use DateTime;

class OpenAiService
{
    private OpenAiRepository $repository;

    public function __construct(OpenAiRepository $repository)
    {
        $this->repository = $repository;
    }

    public function makePost(DateTime $date): array
    {
        $author = $this->makeAuthor();
        $message = $this->makeSystemMessage($author);
        $article = $this->makeArticle($message, $date);
        $title = $this->makeTitle($article);
        return compact('author', 'title', 'article');
    }

    public function makeArticle(string $systemMessage, DateTime $date): string
    {
        $this->repository->setMessage($systemMessage, OpenAiRoleEnum::System);
        $this->repository->setMessage($date->format('n月j日'));
        $response = $this->repository->excute();
        if (empty($response)) {
            throw new \Exception('API処理でエラーが発生しました。');
        }
        return $response['choices'][0]['message']['content'];
    }

    public function makeTitle(string $article): string
    {
        $this->repository->setMessage("{$article}\nこの文章にタイトルをつけてください。", OpenAiRoleEnum::System);
        $response = $this->repository->excute();
        return empty($response) ? '' : $response['choices'][0]['message']['content'];
    }


    private function makeSystemMessage(string $author): string
    {
        return <<<MESSAGE
あなたは{$author}です。
ユーザーから入力される日にちに関する記事を書いてください。
{$author}が書くような内容と文体にしてください。
MESSAGE;
    }

    private function makeAuthor(): string
    {
        $genre = AiGenreEnum::randomValue();
        $adjective = AiAdjectiveEnum::randomValue();
        $personality = AiPersonalityEnum::randomValue();
        $generation = AiGenerationEnum::randomValue();
        return "{$genre}に詳しい{$adjective}で{$personality}な{$generation}";
    }
}

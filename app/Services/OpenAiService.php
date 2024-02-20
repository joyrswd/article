<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OpenAiRepository;
use App\Enums\OpenAiRoleEnum;
use App\Enums\AiGenreEnum;
use App\Enums\AiAdjectiveEnum;
use App\Enums\AiPersonalityEnum;
use App\Enums\AiGenerationEnum;
use Illuminate\Support\Facades\App;
use DateTime;

class OpenAiService
{
    private OpenAiRepository $repository;
    private array $attributes=[];
    private array $conditions = [];
    private string $locale = '';

    public function __construct(OpenAiRepository $repository, ?array $conditions = [])
    {
        $this->repository = $repository;
        $this->conditions = $conditions;
        $this->locale = App::currentLocale();
    }

    public function makePost(DateTime $date): array
    {
        $author = $this->makeAuthor();
        $article = $this->makeArticle($author, $date);
        $title = $this->makeTitle($article);
        $attributes = $this->attributes;
        $locale = $this->locale;
        $model = $this->repository->getModel();
        return compact('title', 'article', 'author', 'attributes', 'locale', 'model');
    }

    public function makeArticle(string $author, DateTime $date): string
    {
        $message = $this->makeSystemMessage($author, $date);
        $this->repository->setMessage($message, OpenAiRoleEnum::System);
        $response = $this->repository->excute();
        if (empty($response)) {
            throw new \Exception('API処理でエラーが発生しました。');
        }
        return $this->repository->getContent($response);
    }

    public function makeTitle(string $article): string
    {
        $this->repository->setMessage("次にユーザーが入力する文章のタイトルを作ってください。", OpenAiRoleEnum::System);
        $this->repository->setMessage($article);
        $response = $this->repository->excute();
        return empty($response) ? '' : $this->repository->getContent($response);
    }

    private function makeSystemMessage(string $author, DateTime $date): string
    {
        $month = $date->format('n月');
        $lang = $this->getLang();
        $message = <<<MESSAGE
あなたは{$author}です。
{$month}にまつわる記事を{$lang}で書いてください。
{$author}が書くような内容と文体にしてください。
MESSAGE;
        if (empty($this->conditions) === false) {
            $message .= "次のルールに従ってください。\n";
            foreach ($this->conditions as $text) {
                $condition = $this->convert($text, $author, $month);
                $message .= "- {$condition}\n";
            }
        }
        return $message;
    }

    private function getLang()
    {
        switch($this->locale) {
            case 'en': return '英語';
            default : return '日本語';
        }
    }

    private function makeAuthor(): string
    {
        $genre = AiGenreEnum::randomValue();
        $adjective = AiAdjectiveEnum::randomValue();
        $personality = AiPersonalityEnum::randomValue();
        $generation = AiGenerationEnum::randomValue();
        $this->attributes = compact('genre', 'adjective', 'personality', 'generation');
        return "{$genre}大好きの{$adjective}で{$personality}な{$generation}";
    }

    private function convert(string $text, string $author, string $month)
    {
        $placeHolder = [
            '{author}' => $author,
            '{month}' => $month,
        ];
        foreach ($this->attributes as $key => $value) {
            $index = '{' . $key . '}';
            $placeHolder[$index] = $value;
        }
        return strtr($text, $placeHolder);
    }
}

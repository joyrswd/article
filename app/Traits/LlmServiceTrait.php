<?php

declare(strict_types=1);

namespace App\Traits;

use App\Interfaces\ApiRepositoryInterface;
use App\Repositories\DeepLRepository;
use App\Repositories\WikipediaRepository;
use App\Enums\AiGenreEnum;
use App\Enums\AiAdjectiveEnum;
use App\Enums\AiPersonalityEnum;
use App\Enums\AiGenerationEnum;
use DateTime;

trait LlmServiceTrait
{
    private ApiRepositoryInterface $repository;
    private array $attributes = [];
    private array $conditions = [];
    private array $roles = [];

    public function makePost(DateTime $date): array
    {
        $author = $this->makeAuthor();
        $article = $this->makeArticle($author, $date);
        if ($this->validateLang($article) === false) {
            $article = $this->translateArticle($article);
        }
        $title = $this->makeTitle($article);
        $attributes = $this->attributes;
        $model = $this->repository->getModel();
        return compact('title', 'article', 'author', 'attributes', 'model');
    }

    private function makeArticle(string $author, DateTime $date): string
    {
        $command = $this->makeCommand($author, $date);
        $this->repository->addPrompt($command);
        $reference = $this->makeReference($date);
        $this->repository->addPrompt($reference);
        $conditions = $this->makeConditons($author);
        $this->repository->addPrompt($conditions);
        $response = $this->repository->requestApi();
        if (empty($response)) {
            throw new \Exception('API処理でエラーが発生しました。');
        }
        return $response;
    }

    private function makeTitle(string $article): string
    {
        $lang = $this->getLang();
        $this->repository->addPrompt("次に入力される文章の『タイトル』を100文字以内の『{$lang}』で作成し、『タイトル』のみを出力してください。");
        $this->repository->addPrompt($article);
        $response = $this->repository->requestApi();
        return empty($response) ? '' : $response;
    }

    private function makeCommand(string $author, DateTime $date): string
    {
        $lang = $this->getLang();
        return <<<MESSAGE
あなたは『{$lang}』を母語とする『{$author}』です。
次に『{$date->format(__("n月j日"))}』に関する情報を示すので、あなたの興味ある情報を選び『{$lang}』で記事を書いてください。
MESSAGE;
    }

    private function makeConditons(string $author): string
    {
        $conditions = [
            "記事の作成は次のルールに従ってください。",
            "- 『{$author}』が書くような文体にしてください。",
            "- 記事にはあなたの考えや感想、体験などを含めてください。",
        ];
        if (empty($this->conditions) === false) {
            foreach ($this->conditions as $text) {
                $condition = $this->convert($text, $author);
                $conditions[] = "- {$condition}";
            }
        }
        return implode("\n", $conditions);
    }

    private function makeReference(DateTime $date) : string
    {
        $wiki = app(WikipediaRepository::class);
        $today = $date->format(__("n月j日"));
        $wiki->addPrompt($today);
        return $wiki->requestApi();
    }

    private function translateArticle(string $article): string
    {
        $translater = app(DeepLRepository::class);
        $translater->setLang(app()->currentLocale());
        $translater->addPrompt($article);
        $response = $translater->requestApi();
        return empty($response) ? '' : $response;
    }

    private function getLang(): string
    {
        switch (app()->currentLocale()) {
            case 'en':
                return '英語';
            default:
                return '日本語';
        }
    }

    private function validateLang(string $article): bool
    {
        if (app()->currentLocale() === 'ja') {
            return true;
        } else {
            return empty(preg_match("/[ぁ-ん]+|[ァ-ヴー]+/u", $article));
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

    private function convert(string $text, string $author): string
    {
        $placeHolder = [
            '{author}' => $author,
        ];
        foreach ($this->attributes as $key => $value) {
            $index = '{' . $key . '}';
            $placeHolder[$index] = $value;
        }
        return strtr($text, $placeHolder);
    }
}

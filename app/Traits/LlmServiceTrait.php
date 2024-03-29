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
        $this->repository->addPrompt("'//start'の後に入力される文章のタイトルを{$lang}で作成してください。");
        $this->repository->addPrompt("タイトルに記号を使用しないでください。");
        $this->repository->addPrompt("出力するのはタイトルのみとしてください。");
        $this->repository->addPrompt("//start");
        $this->repository->addPrompt($article);
        $response = $this->repository->requestApi();
        return empty($response) ? '' : $response;
    }

    private function makeCommand(string $author, DateTime $date): string
    {
        $lang = $this->getLang();
        return <<<MESSAGE
あなたは『{$lang}』を母語とする『{$author}』です。
次に『{$date->format(__("n月j日"))}』に関する情報を示すので、あなたの興味ある情報を選び記事を書いてください。
MESSAGE;
    }

    private function makeConditons(string $author): string
    {
        $lang = $this->getLang();
        $conditions = [
            "記事の作成は次のルールに従ってください。",
            "- 『{$lang}』で書いてください。",
            "- 『{$author}』が書くような文体にしてください。",
            "- 箇条書きにせず、エッセイのようにしてください。",
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
        $response = $wiki->requestApi();
        $info = collect($response)->except([0, __('忌日'), __('出典'), __('関連項目')])->toArray();
        return $this->convertToText($info);
    }

    private function convertToText(array $data) : string
    {
        $texts = "";
        foreach ($data as $title => $values) {
            $texts .= "# {$title} #\n";
            $texts .= collect($values)->flatten()->implode("\n") . "\n\n";
        }
        return $texts;
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

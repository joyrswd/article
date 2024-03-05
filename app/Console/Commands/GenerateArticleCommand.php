<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\OpenAiService;
use App\Services\GoogleAiService;
use App\Services\LlamaService;
use App\Services\ClaudeService;
use App\Services\AttributeService;
use App\Services\AuthorService;
use App\Services\ArticleService;
use App\Services\ImageService;
use App\Services\RssService;
use App\Interfaces\AiImageServiceInterface;
use Illuminate\Console\Command;

class GenerateArticleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:article {llm?} {locale?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private AttributeService $attributeService;
    private AuthorService $authorService;
    private ArticleService $articleService;
    private ImageService $imageService;
    private RssService $rssService;

    public function __construct(
        AttributeService $attributeService,
        AuthorService $authorService,
        ArticleService $articleService,
        ImageService $imageService,
        RssService $rssService,
    ) {
        parent::__construct();
        $this->attributeService = $attributeService;
        $this->authorService = $authorService;
        $this->articleService = $articleService;
        $this->imageService = $imageService;
        $this->rssService = $rssService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //LLM設定
        $llm = match ($this->argument('llm')) {
            'google' => GoogleAiService::class,
            'openai' => OpenAiService::class,
            'claude' => ClaudeService::class,
            default => OpenAiService::class,
        };
        //言語設定
        $locale = $this->argument('locale');
        if (in_array($locale, ['ja', 'en'])) {
            app()->setLocale($locale);
        }
        //文書生成実行
        $service = app($llm);
        $textResponse = $service->makePost(new \DateTime());
        $article = $this->saveArticle(...$textResponse);
        if ($service instanceof AiImageServiceInterface) {
            //画像生成実行
            $this->saveImage($article['id'], $article['content'], $service);
        }
        //RSSの更新
        $this->updateRss();
    }

    private function saveArticle(string $title, string $article, string $author, array $attributes, string $model): array
    {
        $attributeRows = $this->attributeService->addOrFind($attributes);
        $authorRow = $this->authorService->addOrFind($author, $attributeRows);
        return $this->articleService->add($authorRow['id'], $title, $article, $model);
    }

    private function saveImage(int $id, string $article, AiImageServiceInterface $service)
    {
        $imageBinary = $service->makeImage($article);
        $imageModel = $service->getImageModel();
        $watermark = '@' . __('site.title') . ' by ' . $imageModel;
        $imagePath = $this->imageService->put($imageBinary, $watermark);
        $this->imageService->add($id, $imagePath, $imageModel);
    }

    private function updateRss()
    {
        $aticles = $this->articleService->find([], ['limit' => 10]);
        $path = $this->rssService->getFilePath();
        $this->rssService->fetchRss($path, $aticles);
    }
}

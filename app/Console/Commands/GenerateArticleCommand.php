<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\OpenAiService;
use App\Services\GoogleAiService;
use App\Services\AttributeService;
use App\Services\AuthorService;
use App\Services\ArticleService;
use App\Services\RssService;
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
    private RssService $rssService;

    public function __construct(AttributeService $attributeService, AuthorService $authorService, ArticleService $articleService, RssService $rssService)
    {
        parent::__construct();
        $this->attributeService = $attributeService;
        $this->authorService = $authorService;
        $this->articleService = $articleService;
        $this->rssService = $rssService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //LLM設定
        $llm = match($this->argument('llm')) {
            'google' => GoogleAiService::class,
            'openai' => OpenAiService::class,
            default => OpenAiService::class,
        };
        //言語設定
        $locale = $this->argument('locale');
        if (in_array($locale,['ja','en'])) {
            app()->setLocale($locale);
        }
        //処理実行
        $service = app($llm);
        $response = $service->makePost(new \DateTime());
        $this->save(...$response);
        $this->updateRss();
    }

    private function save(string $title, string $article, string $author, array $attributes, string $model):void
    {
        $attributeRows = $this->attributeService->addOrFind($attributes);
        $authorRow = $this->authorService->addOrFind($author, $attributeRows);
        $this->articleService->add($authorRow['id'], $title, $article, $model);
    }

    private function updateRss()
    {
        $aticles = $this->articleService->find([], ['limit' => 10]);
        $path = $this->rssService->getFilePath();
        $this->rssService->fetchRss($path, $aticles);
    }
}

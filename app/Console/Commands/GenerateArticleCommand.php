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
use Illuminate\Support\Facades\Log;

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
        $textResponse = $service->makePost(new \DateTime());
        $article = $this->saveArticle(...$textResponse);
        $imageResponse = $service->makeImage($article['content']);
        if (empty($imageResponse) === false) {
            $this->addImage($article['id'], ...$imageResponse);
        }
        $this->updateRss();
    }

    private function saveArticle(string $title, string $article, string $author, array $attributes, string $model):array
    {
        $attributeRows = $this->attributeService->addOrFind($attributes);
        $authorRow = $this->authorService->addOrFind($author, $attributeRows);
        return $this->articleService->add($authorRow['id'], $title, $article, $model);
    }

    private function addImage(int $articleId, string $url, string $description, string $size, string $model):void
    {
        try {
            $path = $this->putImage($url);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return ;
        }
        
    }

    private function putImage(string $url) : string
    {
        $image = file_get_contents($url);
        if (empty($image)) {
            new \Exception('ファイル取得失敗');
        }
        $finfo = new \finfo(FILEINFO_EXTENSION);
        $extension = $finfo->buffer($image);
        $dir = $this->setUpDirectory(['img', 'posts', date('Y'), date('m'), date('d')]);
        $path = $dir . md5($url) . '.' . $extension;
        if(file_put_contents($path, $image) ) {
            return $path;
        } else {
            new \Exception('ファイル保存失敗');
        }
    }

    private function setUpDirectory($dirs) 
    {
        $dir = public_path() . '/';
        foreach($dirs as $name) {
            $dir .= $name . '/';
            if (is_dir($dir) === false) {
                mkdir($dir);
            }
        }
        return $dir;
    }

    private function updateRss()
    {
        $aticles = $this->articleService->find([], ['limit' => 10]);
        $path = $this->rssService->getFilePath();
        $this->rssService->fetchRss($path, $aticles);
    }
}

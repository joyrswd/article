<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\OpenAiService;
use App\Services\AttributeService;
use App\Services\AuthorService;
use App\Services\ArticleService;
use Illuminate\Console\Command;

class GenerateArticleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:article';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private OpenAiService $llmService;
    private AttributeService $attributeService;
    private AuthorService $authorService;
    private ArticleService $articleService;

    public function __construct(OpenAiService $openAiService, AttributeService $attributeService, AuthorService $authorService, ArticleService $articleService)
    {
        parent::__construct();
        $this->llmService = $openAiService;
        $this->attributeService = $attributeService;
        $this->authorService = $authorService;
        $this->articleService = $articleService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = $this->llmService->makePost(new \DateTime());
        $this->save(...$response);
        //
    }

    private function save(string $argTitle, string $argArticle, string $argAuthor, array $argAttributes, string $argModel):void
    {
        $attributes = $this->attributeService->addOrFind($argAttributes);
        $author = $this->authorService->addOrFind($argAuthor, $attributes);
        $this->articleService->add($author['id'], $argTitle, $argArticle, $argModel);
    }
}

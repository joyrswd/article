<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ArticleRepository;
use Illuminate\Support\Facades\App;

class ArticleService
{

    private ArticleRepository $repository;

    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function add(int $authorId, string $title, string $content, string $llmName) : array
    {
        $locale = App::currentLocale();
        $id = $this->repository->create([
            'author_id' => $authorId,
            'title' => $title,
            'content' => $content,
            'llm_name' => $llmName,
            'locale' => $locale
        ]);
        return $this->repository->read($id);
    }

    public function get(int $id) :array
    {
        return $this->repository->read($id);
    }

    public function find(array $param) :array
    {
        return $this->repository->find($param);
    }

}
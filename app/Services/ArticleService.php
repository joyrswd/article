<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ArticleRepository;
use App\Repositories\AuthorRepository;
use Illuminate\Support\Facades\App;

class ArticleService
{

    private ArticleRepository $repository;
    private AuthorRepository $authorRepository;

    public function __construct(ArticleRepository $repository, AuthorRepository $authorRepository)
    {
        $this->repository = $repository;
        $this->authorRepository = $authorRepository;
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
        return $this->repository->findOne([
            'id' => $id,
            'locale' => App::currentLocale(),
        ]);
    }

    public function getWithAttributes(int $id) :array
    {
        $article = $this->get($id);
        if (empty($article)) {
            return [];
        }
        $author = $this->authorRepository->read($article['author_id']);
        $article['author']['attributes'] = $author['attributes'];
        return $article;
    }

    public function find(array $param, ?array $options = []) :array
    {
        $param['locale'] = App::currentLocale();
        return $this->repository->find($param, $options);
    }

}
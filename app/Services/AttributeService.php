<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AttributeRepository;
use App\Repositories\ArticleRepository;

class AttributeService
{

    private AttributeRepository $repository;
    private ArticleRepository $articleRepository;

    public function __construct(AttributeRepository $repository, ArticleRepository $articleRepository)
    {
        $this->repository = $repository;
        $this->articleRepository = $articleRepository;
    }

    public function addOrFind(array $params): array
    {
        $records = [];
        foreach ($params as $key => $value) {
            $record = $this->repository->findOne(['name' => $value]);
            if (empty($record)) {
                $id = $this->repository->create(['name' => $value, 'type' => $key]);
                $record = $this->repository->read($id);
            }
            $records[] = $record;
        }
        return $records;
    }

    public function findWithArticles(string $attr): array
    {
        $attribute = $this->repository->findOne(['id' => $attr]);
        $ids = array_column($attribute['authors'], 'id');
        $articles = $this->articleRepository->find([
            'locale' =>app()->currentLocale()
        ], ['whereIn' => ['author_id', $ids], 'orderBy' => ['created_at', 'desc']]);
        if (empty($articles) === false) {
            $attribute['articles'] = $articles;
        }
        return $attribute;
    }
}

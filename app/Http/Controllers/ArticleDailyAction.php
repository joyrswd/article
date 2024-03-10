<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Http\Resources\ArticleResourceCollection;
use Illuminate\Http\Request;

final class ArticleDailyAction extends Controller
{

    private ArticleService $service;

    public function __construct(ArticleService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(string $date, Request $request)
    {
        $articles = $this->service->find([
            ['created_at', '>=',  new \DateTime($date)],
            ['created_at', '<',  new \DateTime($date . ' + 1day')],
        ]);
        $resource = new ArticleResourceCollection($articles);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

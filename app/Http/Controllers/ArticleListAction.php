<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Http\Resources\ArticleResoruceCollection;
use Illuminate\Http\Request;

final class ArticleListAction extends Controller
{

    private ArticleService $service;

    public function __construct(ArticleService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(Request $request)
    {
        $articles = $this->service->find($request->all());
        $resource = new ArticleResoruceCollection($articles);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

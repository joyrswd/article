<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Request;

final class ArticleAction extends Controller
{

    private ArticleService $service;

    public function __construct(ArticleService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(int $post, Request $request)
    {
        $aticle = $this->service->getWithAttributes($post);
        $resource = new ArticleResource($aticle);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

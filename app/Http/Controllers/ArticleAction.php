<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Http\Resources\ArticleResoruce;
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
        $aticle = $this->service->get($post);
        $resource = new ArticleResoruce($aticle);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

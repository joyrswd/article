<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Http\Resources\ArticleResoruceCollection;
use Illuminate\Http\Request;

final class HomeAction extends Controller
{

    private ArticleService $service;

    public function __construct(ArticleService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(Request $request)
    {
        $aticle = $this->service->find([], ['limit' => 10, 'orderBy' => ['created_at', 'desc']]);
        $resource = new ArticleResoruceCollection($aticle);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
}

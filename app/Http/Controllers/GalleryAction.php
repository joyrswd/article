<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use App\Http\Resources\ImageResourceCollection;
use Illuminate\Http\Request;

final class GalleryAction extends Controller
{

    private ImageService $service;

    public function __construct(ImageService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(Request $request)
    {
        $articles = $this->service->find([], ['limit' => 12]);
        if (empty($articles)) {
            abort(404);
        }
        $resource = new ImageResourceCollection($articles);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

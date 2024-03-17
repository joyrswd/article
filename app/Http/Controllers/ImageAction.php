<?php

namespace App\Http\Controllers;

use App\Services\ImageService;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Request;

final class ImageAction extends Controller
{

    private ImageService $service;

    public function __construct(ImageService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(int $id, Request $request)
    {
        $image = $this->service->get($id);
        if (empty($image)) {
            abort(404);
        }
        $resource = new ImageResource($image);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

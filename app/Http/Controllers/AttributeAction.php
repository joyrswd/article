<?php

namespace App\Http\Controllers;

use App\Services\AttributeService;
use App\Http\Resources\AttributeResource;
use Illuminate\Http\Request;

final class AttributeAction extends Controller
{

    private AttributeService $service;

    public function __construct(AttributeService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(int $attr, Request $request)
    {
        $attribute = $this->service->findWithArticles($attr);
        $resource = new AttributeResource($attribute);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

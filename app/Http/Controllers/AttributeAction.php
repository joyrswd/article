<?php

namespace App\Http\Controllers;

use App\Services\AttributeService;
use App\Http\Resources\AttributeResoruce;
use Illuminate\Http\Request;

final class AttributeAction extends Controller
{

    private AttributeService $service;

    public function __construct(AttributeService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(string $attr, Request $request)
    {
        $attribute = $this->service->findWithArticles($attr);
        $resource = new AttributeResoruce($attribute);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

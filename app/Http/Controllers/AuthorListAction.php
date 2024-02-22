<?php

namespace App\Http\Controllers;

use App\Services\AuthorService;
use App\Http\Resources\AuthorResoruceCollection;
use Illuminate\Http\Request;

final class AuthorListAction extends Controller
{

    private AuthorService $service;

    public function __construct(AuthorService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(Request $request)
    {
        $authors = $this->service->find($request->all());
        $resource = new AuthorResoruceCollection($authors);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

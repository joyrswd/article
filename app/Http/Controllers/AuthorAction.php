<?php

namespace App\Http\Controllers;

use App\Services\AuthorService;
use App\Http\Resources\AuthorResoruce;
use Illuminate\Http\Request;

final class AuthorAction extends Controller
{

    private AuthorService $service;

    public function __construct(AuthorService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(int $user, Request $request)
    {
        $author = $this->service->get($user);
        $resource = new AuthorResoruce($author);
        return $resource->response($request)->header('content-type', 'application/hal+json');
    }
    //
}

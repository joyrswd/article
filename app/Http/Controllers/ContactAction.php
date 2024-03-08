<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use App\Http\Requests\ContactRequest;
use Illuminate\Http\Request;

final class ContactAction extends Controller
{

    private ContactService $service;

    public function __construct(ContactService $service)
    {
        $this->service  = $service;
    }

    public function __invoke(ContactRequest $request)
    {
        // slackに通知
        $this->service->send($request->input('email'), $request->input('message'));
        return response()->json(['result' => true]);
    }
    //
}

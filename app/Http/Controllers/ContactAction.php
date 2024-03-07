<?php

namespace App\Http\Controllers;

//use App\Services\ContactService;
use Illuminate\Http\Request;

final class ContactAction extends Controller
{

    //private ArticleService $service;

    public function __construct()
    {
        //$this->service  = $service;
    }

    public function __invoke(Request $request)
    {
        // slackに通知
        $url = env('CONTACT_SLACK_WEBHOOK_URL');
        $text = "お問い合わせがありました。\n";
        $text .= "メールアドレス: " . $request->input('email') . "\n";
        $text .= "内容: " . $request->input('message') . "\n";
        $data = [
            'text' => $text,
        ];
        $data = json_encode($data);
        $options = array(
            'http' => array(
                'header' => "Content-Type: application/json",
                'method' => 'POST',
                'content' => $data,
            ),
        );
        $context = stream_context_create($options);
        file_get_contents($url, false, $context);
        return response()->json(['message' => 'お問い合わせを受け付けました。']);
    }
    //
}

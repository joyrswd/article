<?php
namespace App\Services;

use Illuminate\Notifications\Notifiable;
use App\Notifications\SlackNotification;

class ContactService
{
    use Notifiable;

    public function send(string $from, string $content)
    { 
        $message = "<mailto:{$from}|{$from}>\n\n{$content}";
        $this->notify(new SlackNotification($message));
    }

    protected function routeNotificationForSlack()
    {
        return config('services.slack.contact');
    }
}

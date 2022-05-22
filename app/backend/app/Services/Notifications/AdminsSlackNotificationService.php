<?php

namespace App\Services\Notifications;

use Illuminate\Notifications\Notifiable;
use App\Notifications\Admins\AdminUpdateNotification;
use Illuminate\Support\Facades\Config;

class AdminsSlackNotificationService
{
    use Notifiable;

    /**
     * send slack notification
     *
     * @param  string $message
     * @param  mixed $attachment
     * @return void
     */
    public function send($message = null, $attachment = null)
    {
        if (Config::get('app.env') !== 'testing') {
            $this->notify(new AdminUpdateNotification($message, $attachment));
        }
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForSlack($notification)
    {
        return Config::get('myapp.slack.url');
    }
}
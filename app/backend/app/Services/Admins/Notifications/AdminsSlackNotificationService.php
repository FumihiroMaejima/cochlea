<?php

namespace App\Services\Admins\Notifications;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use App\Notifications\Admins\AdminUpdateNotification;
use App\Services\Admins\Notifications\BaseSlackNotificationService;

class AdminsSlackNotificationService extends BaseSlackNotificationService
{
    /**
     * send slack notification
     *
     * @param  string $message
     * @param  array $attachment
     * @return void
     */
    public function send(string $message = null, array $attachment = null): void
    {
        if (Config::get('app.env') !== 'testing') {
            $this->notify(new AdminUpdateNotification($message, $attachment));
        }
    }
}

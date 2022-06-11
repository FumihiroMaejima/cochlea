<?php

namespace App\Services\Notifications;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use App\Notifications\Admins\RoleUpdateNotification;
use App\Services\Notifications\BaseSlackNotificationService;

class RoleSlackNotificationService extends BaseSlackNotificationService
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
            $this->notify(new RoleUpdateNotification($message, $attachment));
        }
    }
}

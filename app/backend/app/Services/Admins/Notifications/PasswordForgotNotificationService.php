<?php

namespace App\Services\Admins\Notifications;

use Illuminate\Notifications\Notifiable;
use App\Notifications\Admins\AdminUpdateNotification;
use App\Notifications\Admins\ResetPasswordNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Passwords\CanResetPassword;

class PasswordForgotNotificationService
{
    use Notifiable;
    use CanResetPassword;

    /**
     * target email.
     *
     * @var string
     */
    private string $email;

    /**
     * Create a notification service instance.
     *
     * @param  string $email
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * send password forgot notification
     *
     * @param  string $token
     * @param  array $attachment
     * @return void
     */
    public function send(string $token): void
    {
        if (Config::get('app.env') !== 'testing') {
            $this->notify(new ResetPasswordNotification($token));
        }
    }
}

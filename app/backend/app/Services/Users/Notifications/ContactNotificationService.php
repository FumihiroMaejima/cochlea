<?php

namespace App\Services\Users\Notifications;

use Illuminate\Notifications\Notifiable;
use App\Notifications\Users\ContactNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Passwords\CanResetPassword;

class ContactNotificationService
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
     * send contact notification
     *
     * @param  string $email
     * @param  string $type
     * @param  string $detail
     * @param  string $failureDetail
     * @param  string $failureTime
     * @return void
     */
    public function send(
        string $email,
        string $type,
        string $detail,
        string $failureDetail,
        string $failureTime
    ): void {
        if (Config::get('app.env') !== 'testing') {
            $this->notify(new ContactNotification(
                $email,
                $type,
                $detail,
                $failureDetail,
                $failureTime
            ));
        }
    }
}

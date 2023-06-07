<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Closure;

// vendor/laravel/framework/src/Illuminate/Auth/Notifications/ResetPassword.phpをコピーして作成
class AuthCodeNotification extends Notification
{
    use Queueable;

    private const DEFAULT_CAHNNEL = 'mail';

    /**
     * The Auth Code.
     *
     * @var string
     */
    protected string $code;

    /**
     * expired At of auth code
     *
     * @var string
     */
    protected string $expiredAt;

    /**
     * The callback that should be used to create the reset password URL.
     *
     * @var (\Closure(mixed, string): string)|null
     */
    public static $createUrlCallback;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var (\Closure(mixed, string): \Illuminate\Notifications\Messages\MailMessage)|null
     */
    public static $toMailCallback;

    /**
     * Create a new notification instance.
     *
     * @param  string $code
     * @param  string $expiredAt
     * @return void
     */
    public function __construct(string $code, string $expiredAt)
    {
        $this->code = $code;
        $this->expiredAt = $expiredAt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [self::DEFAULT_CAHNNEL];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->code, $this->expiredAt);
        }

        return $this->buildMailMessage($this->code, $this->expiredAt);
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string $code
     * @param  string $expiredAt
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage(string $code, string $expiredAt)
    {
        // TODO フロントエンドに合わせてURLやフォーマットの変更
        return (new MailMessage())
            ->subject('Auth Code Notification')
            ->line('You are receiving this email because we received a authentication request for your account.')
            // ->action(Lang::get('Reset Password'), $url)
            ->line('Auth Code is :code.', ['code' => $code])
            ->line('This Auth Code will expire in :count minutes.', ['count' => $expiredAt])
            ->line('If you did not request, no further action is required.');
    }

    /**
     * Set a callback that should be used when creating the reset password button URL.
     *
     * @param  Closure(mixed, string): string  $callback
     * @return void
     */
    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  Closure(mixed, string): \Illuminate\Notifications\Messages\MailMessage  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}

<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Closure;

// vendor/laravel/framework/src/Illuminate/Auth/Notifications/ResetPassword.phpをコピーして作成
class ContactNotification extends Notification
{
    use Queueable;

    private const DEFAULT_CAHNNEL = 'mail';
    private const DEFAULT_SUB_CAHNNEL = 'slack';

    /**
     * email
     *
     * @var string
     */
    protected string $email;

    /**
     * contacat type
     *
     * @var string
     */
    protected string $type;

    /**
     * detail of contact
     *
     * @var string
     */
    protected string $detail;

    /**
     * detail  of system failure
     *
     * @var string
     */
    protected string $failureDetail;

    /**
     * failure At of auth code
     *
     * @var string
     */
    protected string $failureTime;

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
     * @param  string $email
     * @param  string $type
     * @param  string $detail
     * @param  string $failureDetail
     * @param  string $failureTime
     * @return void
     */
    public function __construct(
        string $email,
        string $type,
        string $detail,
        string $failureDetail,
        string $failureTime
    ) {
        $this->email = $email;
        $this->type = $type;
        $this->detail = $detail;
        $this->failureDetail = $failureDetail;
        $this->failureTime = $failureTime;
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

        return $this->buildMailMessage(
            $this->email,
            $this->type,
            $this->detail,
            $this->failureDetail,
            $this->failureTime
        );
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string $email
     * @param  string $type
     * @param  string $detail
     * @param  string $failureDetail
     * @param  string $failureTime
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage(
        string $email,
        string $type,
        string $detail,
        string $failureDetail,
        string $failureTime
        ) {
        // TODO フロントエンドに合わせてURLやフォーマットの変更
        return (new MailMessage())
            ->subject('Contact Notification')
            ->line('You are receiving this email because we received a contact request for you.')
            ->line("Email: $email")
            ->line("Type: $type")
            ->line("Detail: $detail")
            ->line("Failure Time: $failureTime")
            ->line("Failure Detail: $failureDetail")
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

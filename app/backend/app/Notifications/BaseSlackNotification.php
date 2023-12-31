<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class BaseSlackNotification extends Notification
{
    use Queueable;

    private const NOTICFICATION_CHANNEL_SLACK = 'slack';

    // attachment
    protected const ATTACHMENT_KEY_PRE_TEXT = 'pretext';
    protected const ATTACHMENT_KEY_TITLE = 'title';
    protected const ATTACHMENT_KEY_TITLE_LINK = 'titleLink';
    protected const ATTACHMENT_KEY_CONTENT = 'content';
    protected const ATTACHMENT_KEY_COLOR = 'color';

    protected string $messageContent = ':book: Check following message.';
    protected string $footerContent = '@';

    protected string $message;
    protected array $attachment;

    /**
     * Create a new notification instance.
     *
     * @param string $message message
     * @param array $attachment slack notification resource
     * @return void
     */
    public function __construct(string $message = '', array $attachment = [])
    {
        $this->message = $message;
        $this->attachment = $attachment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // return ['mail'];
        return [self::NOTICFICATION_CHANNEL_SLACK];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    /* public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    } */

    /**
     * Get the Slack representation of the notification.
     * ex: tada, gift, camera, computer, iphone, lock, key, memo, book, black_square_button, clipboard, calendar, email
     *
     * @param  mixed  $notifiable - class call this method.
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->from(Config::get('app.name') . ': ' . Config::get('myapp.slack.name'), Config::get('myapp.slack.icon'))
            ->to(Config::get('myapp.slack.channel'))
            ->content($this->messageContent . "\n" . $this->message)
            ->attachment(function ($attachment) {
                if (!empty($this->attachment)) {
                    // Illuminate\Notifications\Messages\SlackAttachment $attachment
                    $attachment->pretext($this->attachment[self::ATTACHMENT_KEY_PRE_TEXT])
                        ->title(
                            $this->attachment[self::ATTACHMENT_KEY_TITLE],
                            $this->attachment[self::ATTACHMENT_KEY_TITLE_LINK]
                        )
                        ->content($this->attachment[self::ATTACHMENT_KEY_CONTENT])
                        ->color($this->attachment[self::ATTACHMENT_KEY_COLOR])
                        ->fields([
                            'ID'     => $this->attachment['id'],
                            'Name'   => $this->attachment['name'],
                            'Status' => $this->attachment['status'],
                            'Detail' => $this->attachment['detail'],
                        ])
                        ->footer($this->footerContent . Config::get('app.name'));
                }
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

<?php

namespace App\Notifications\Admins;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class AdminUpdateNotification extends Notification
{
    use Queueable;

    private const NOTICFICATION_CHANNEL_SLACK = 'slack';

    protected $message;
    protected $attachment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message = null, $attachment = null)
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
        return (new SlackMessage)
            ->from(Config::get('app.name') . ': ' . Config::get('myapp.slack.name'), Config::get('myapp.slack.icon'))
            ->to(Config::get('myapp.slack.channel'))
            ->content(':book: Check following message.' . "\n" . $this->message)
            ->attachment(function ($attachment) {
                if ($this->attachment) {
                    // Illuminate\Notifications\Messages\SlackAttachment $attachment
                    $attachment->pretext($this->attachment['pretext'])
                        ->title($this->attachment['title'], $this->attachment['titleLink'])
                        ->content($this->attachment['content'])
                        ->color($this->attachment['color'])
                        ->fields([
                            'ID'     => $this->attachment['id'],
                            'Name'   => $this->attachment['name'],
                            'Status' => $this->attachment['status'],
                            'Detail' => $this->attachment['detail'],
                        ])
                        ->footer('@' . Config::get('app.name'));
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

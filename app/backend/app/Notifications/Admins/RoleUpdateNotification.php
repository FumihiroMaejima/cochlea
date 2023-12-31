<?php

declare(strict_types=1);

namespace App\Notifications\Admins;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use App\Notifications\BaseSlackNotification;

class RoleUpdateNotification extends BaseSlackNotification
{
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
}

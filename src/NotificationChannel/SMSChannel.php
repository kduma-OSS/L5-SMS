<?php
namespace KDuma\SMS\NotificationChannel;

use Illuminate\Notifications\Notification;
use KDuma\SMS\SMSManager;

class SMSChannel
{
    /**
     * @var SMSManager;
     */
    protected $SMSManager;

    /**
     * @param SMSManager $SMSManager
     */
    public function __construct(SMSManager $SMSManager)
    {
        $this->SMSManager = $SMSManager;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$to = $notifiable->routeNotificationFor('SMS')) {
            return;
        }

        $message = $notification->toSMS($notifiable);
        if (is_string($message)) {
            $message = new SMSMessage($message);
        }

        $this->SMSManager
            ->channel($message->channel)
            ->send($to, $message->content);
    }
}
<?php

namespace Atin\LaravelSubscription\Notifications;

use Atin\LaravelSubscription\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class IncompleteSubscription extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Subscription $subscription
    ) {}

    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->line((app()->isProduction() ? '' : 'TEST ').'*[Incomplete Subscription]*')
            ->line('_ID:_ '.$this->subscription->id)
            ->line('_User ID:_ '.$this->subscription->user->id)
            ->line('_User Email:_ '.$this->subscription->user->email)
            ->line('_User Name:_ '.$this->subscription->user->name);
    }
}

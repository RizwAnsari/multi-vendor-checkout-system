<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Customer\Order;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewOrderVendorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Order $order
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Order Received - #' . $this->order->id)
            ->greeting('Hello ' . $this->order->vendor->name . '!')
            ->line('You have received a new order.')
            ->line('Order ID: #' . $this->order->id)
            ->line('Order Total: ' . number_format($this->order->subtotal, 2))
            ->action('View Order Details', url('/vendor/orders/' . $this->order->id))
            ->line('Please process this order as soon as possible.');
    }
}

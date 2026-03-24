<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\NewOrderVendorNotification;

class NotifyVendorOfNewOrder implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order;
        $vendor = $order->vendor;

        if ($vendor && $vendor->user) {
            $vendor->user->notify(new NewOrderVendorNotification($order));
        }
    }
}

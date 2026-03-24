<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateOrderInvoice implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment;
        $order = $payment->order;

        Log::info("Invoice generated for Order #{$order->id} following payment of {$payment->amount}");
    }
}

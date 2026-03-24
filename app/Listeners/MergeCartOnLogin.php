<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\Customer\CartService;

class MergeCartOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct(protected CartService $cartService) {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $this->cartService->mergeGuestCartIntoUser($event->user);
    }
}

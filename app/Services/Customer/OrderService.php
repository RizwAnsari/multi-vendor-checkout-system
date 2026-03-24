<?php

namespace App\Services\Customer;

use App\Models\User;
use App\Models\Product;
use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Enums\PaymentStatus;
use App\Models\Customer\Order;
use App\Events\PaymentSucceeded;
use App\Models\Customer\Payment;
use App\Models\Customer\OrderItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Exceptions\EmptyCartException;
use App\Services\Customer\CartService;
use App\Exceptions\InsufficientStockException;

class OrderService
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Process the entire checkout flow across multiple vendors.
     * 
     * @return Collection Collection of created Order models.
     * @throws EmptyCartException|InsufficientStockException
     */
    public function processCheckout(?User $user): Collection
    {
        if ($this->cartService->isEmpty($user)) {
            throw new EmptyCartException("Cannot checkout with an empty cart.");
        }

        $groupedItems = $this->cartService->getGroupedItems($user);

        return DB::transaction(function () use ($user, $groupedItems) {
            $createdOrders = collect();

            foreach ($groupedItems as $vendorId => $items) {
                // 1. Calculate Subtotal for this vendor group
                $subtotal = $items->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                });

                // 2. Create the Vendor Order
                $order = Order::create([
                    'user_id' => $user?->id,
                    'vendor_id' => $vendorId,
                    'subtotal' => $subtotal,
                    'status' => OrderStatus::PENDING,
                ]);

                // 3. Process each item (Stock + OrderItem)
                foreach ($items as $item) {
                    $this->processOrderItem($order, $item);
                }

                // 4. Create Simulated Payment
                $this->createSimulatedPayment($order);

                // Dispatch OrderPlaced Event
                event(new OrderPlaced($order));

                $createdOrders->push($order);
            }

            // 5. Post-Checkout Cleanup
            $this->cartService->clear($user);

            return $createdOrders;
        });
    }

    /**
     * Handles stock deduction and item creation.
     */
    protected function processOrderItem(Order $order, object $item): void
    {
        $product = Product::active()->lockForUpdate()->find($item->product->id);

        if ($product?->isOutOfStock($item->quantity)) {
            throw new InsufficientStockException(
                "Stock for '{$item->product->name}' became insufficient during checkout."
            );
        }

        // Atomic Stock Deduction
        $product->decrement('stock', $item->quantity);

        // Create Order Item record
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item->product->id,
            'product_name' => $item->product->name,
            'unit_price' => $item->product->price,
            'quantity' => $item->quantity,
            'line_total' => $item->product->price * $item->quantity,
        ]);
    }

    /**
     * Generates a payment record marked as completed.
     */
    protected function createSimulatedPayment(Order $order): void
    {
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $order->subtotal,
            'status' => PaymentStatus::PAID,
            'transaction_ref' => 'PAY-' . strtoupper(bin2hex(random_bytes(6))),
            'paid_at' => now(),
        ]);

        // Dispatch PaymentSucceeded Event
        event(new PaymentSucceeded($payment));

        $order->update(['status' => OrderStatus::CONFIRMED]);
    }
}

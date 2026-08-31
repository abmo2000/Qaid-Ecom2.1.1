<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminOrderNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerOrderConfirmation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCreationListener implements ShouldQueue
{
     use InteractsWithQueue;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
     public $tries = 3;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
             Mail::to($event->order->customer->email)
            ->send(new CustomerOrderConfirmation($event->order, $event->items, $event->totalAmount));

        // Send email to admin
        $adminEmail = config('mail.to.address')
            ?? getBuisnessSettings('buisness-info')?->email
            ?? config('mail.from.address');
        Mail::to($adminEmail)
            ->send(new AdminOrderNotification($event->order, $event->totalAmount));
    }

    /**
     * Handle a job failure.
     */
    public function failed(OrderCreated $event, \Throwable $exception): void
    {
        // Log the failure or notify admins
        Log::error('Order notification email failed', [
            'order_id' => $event->order->id,
            'error' => $exception->getMessage()
        ]);
    }
}

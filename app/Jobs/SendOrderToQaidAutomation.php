<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\QaidWhatsAppAutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendOrderToQaidAutomation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public int $timeout = 15;

    public function __construct(
        public Order $order,
        public string $locale,
    ) {
    }

    public function handle(QaidWhatsAppAutomationService $automation): void
    {
        try {
            $automation->sendOrder($this->order, $this->locale);
        } catch (Throwable $exception) {
            Log::warning('QAID order automation submission failed; it will be retried.', [
                'order_id' => $this->order->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error('QAID order automation submission failed permanently.', [
            'order_id' => $this->order->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
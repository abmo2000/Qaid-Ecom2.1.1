<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\BuisnessSetting;
use App\Services\EvolutionWhatsAppService;
use App\Services\PhoneNumberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWhatsAppOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(
        public Order $order,
        public int $totalAmount,
        public string $locale,
    ) {
    }

    public function handle(EvolutionWhatsAppService $whatsApp): void
    {
        $phone = $this->order->customer_phone;

        if (! $phone) {
            Log::info('WhatsApp order confirmation skipped because the customer has no phone number.', [
                'order_id' => $this->order->id,
            ]);

            return;
        }

        try {
            $phoneNumber = new PhoneNumberService((string) $phone);

            if (! $phoneNumber->isValid()) {
                Log::warning('WhatsApp order confirmation skipped because the phone number is invalid.', [
                    'order_id' => $this->order->id,
                ]);

                return;
            }

            $sent = $whatsApp->sendText(
                $phoneNumber->formattedNumber(),
                $this->message(),
            );
        } catch (Throwable $exception) {
            Log::warning('WhatsApp order confirmation could not be prepared.', [
                'order_id' => $this->order->id,
                'error' => $exception->getMessage(),
            ]);

            return;
        }

        if (! $sent) {
            throw new \RuntimeException('Evolution API could not send the order confirmation.');
        }

        $this->order->forceFill(['whatsapp_sent_at' => now()])->save();
    }

    private function message(): string
    {
        $setting = BuisnessSetting::query()->where('key', 'whatsapp-message')->first();
        $template = data_get($setting?->translate($this->locale)?->value, 'message')
            ?? data_get($setting?->translate('en')?->value, 'message')
            ?? EvolutionWhatsAppService::DEFAULT_MESSAGE_TEMPLATES[$this->locale]
            ?? EvolutionWhatsAppService::DEFAULT_MESSAGE_TEMPLATES['en'];

        return strtr($template, [
            '{customer_name}' => $this->order->customer_name,
            '{order_number}' => (string) $this->order->id,
            '{order_total}' => number_format($this->totalAmount, 2),
        ]);
    }
}
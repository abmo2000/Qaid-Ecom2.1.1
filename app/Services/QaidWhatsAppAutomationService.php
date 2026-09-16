<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QaidWhatsAppAutomationService
{
    public function sendOrder(Order $order, string $locale): bool
    {
        $url = config('services.qaid_automation.url');
        $secret = config('services.qaid_automation.secret');

        if (! $url || ! $secret) {
            Log::warning('QAID order automation is not configured.', [
                'order_id' => $order->id,
            ]);

            return false;
        }

        $phone = $order->customer_phone;

        if (! $phone) {
            Log::warning('QAID order automation skipped because the customer has no phone number.', [
                'order_id' => $order->id,
            ]);

            return false;
        }

        $phoneNumber = new PhoneNumberService((string) $phone, 'EG');

        if (! $phoneNumber->isValid()) {
            Log::warning('QAID order automation skipped because the phone number is invalid.', [
                'order_id' => $order->id,
            ]);

            return false;
        }

        $payload = [
            'event' => 'order.created',
            'order_id' => $order->id,
            'order_number' => (string) ($order->order_id ?: 'ORD-'.$order->id),
            'customer_name' => $order->customer_name,
            'phone' => ltrim($phoneNumber->formattedNumber(), '+'),
            'total' => (float) $order->amount,
            'currency' => 'EGP',
            'locale' => $locale,
            'created_at' => $order->created_at?->toISOString() ?? now()->toISOString(),
        ];

        $response = $this->request($secret, 'order-'.$order->id)->post($url, $payload);

        if (! $response->successful()) {
            throw new \RuntimeException(sprintf(
                'QAID automation returned HTTP %d for order %s.',
                $response->status(),
                $order->id,
            ));
        }

        Log::info('Order submitted to QAID WhatsApp automation.', [
            'order_id' => $order->id,
        ]);

        return true;
    }

    private function request(string $secret, string $eventId): PendingRequest
    {
        return Http::asJson()
            ->acceptJson()
            ->withHeaders([
                'X-QAID-SECRET' => $secret,
            ])
            ->withHeaders([
                'X-QAID-EVENT-ID' => $eventId,
            ])
            ->timeout(15);
    }
}
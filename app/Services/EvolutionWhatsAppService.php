<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class EvolutionWhatsAppService
{
    public const DEFAULT_MESSAGE_TEMPLATES = [
        'en' => 'Hi {customer_name}, your order #{order_number} for {order_total} EGP has been confirmed. Thanks for shopping with QAID!',
        'ar' => 'مرحباً {customer_name}، تم تأكيد طلبك رقم #{order_number} بقيمة {order_total} جنيه. شكراً لتسوقك مع QAID!',
    ];

    public function connectionState(): array
    {
        $configuration = $this->configuration();

        if ($configuration === null) {
            return ['state' => 'close', 'phone' => null];
        }

        try {
            $response = $this->request($configuration['key'])
                ->get($configuration['url'] . '/instance/connectionState/' . rawurlencode($configuration['instance']));

            if (! $response->successful()) {
                Log::warning('Evolution API connection status check failed.', ['status' => $response->status()]);

                return ['state' => 'close', 'phone' => null];
            }

            $data = $response->json();
            $instance = is_array($data['instance'] ?? null) ? $data['instance'] : $data;

            return [
                'state' => strtolower((string) ($instance['state'] ?? $data['state'] ?? 'close')),
                'phone' => $this->phoneFromResponse($data),
            ];
        } catch (Throwable $exception) {
            Log::warning('Evolution API connection status request failed.', ['error' => $exception->getMessage()]);

            return ['state' => 'close', 'phone' => null];
        }
    }

    public function connect(): ?string
    {
        $configuration = $this->configuration();

        if ($configuration === null) {
            return null;
        }

        try {
            $response = $this->request($configuration['key'])
                ->get($configuration['url'] . '/instance/connect/' . rawurlencode($configuration['instance']));

            if (! $response->successful()) {
                Log::warning('Evolution API QR request failed.', ['status' => $response->status()]);

                return null;
            }

            $data = $response->json();

            return $data['base64']
                ?? $data['qrcode']['base64']
                ?? $data['qrCode']['base64']
                ?? null;
        } catch (Throwable $exception) {
            Log::warning('Evolution API QR request failed.', ['error' => $exception->getMessage()]);

            return null;
        }
    }

    public function logout(): bool
    {
        $configuration = $this->configuration();

        if ($configuration === null) {
            return false;
        }

        try {
            $response = $this->request($configuration['key'])
                ->delete($configuration['url'] . '/instance/logout/' . rawurlencode($configuration['instance']));

            if (! $response->successful()) {
                Log::warning('Evolution API logout failed.', ['status' => $response->status()]);
            }

            return $response->successful();
        } catch (Throwable $exception) {
            Log::warning('Evolution API logout request failed.', ['error' => $exception->getMessage()]);

            return false;
        }
    }

    public function sendText(string $phoneNumber, string $message): bool
    {
        $configuration = $this->configuration();

        if ($configuration === null) {
            Log::warning('Evolution API is not configured; WhatsApp message was not sent.');

            return false;
        }

        try {
            $response = $this->request($configuration['key'])
                ->post($configuration['url'] . '/message/sendText/' . rawurlencode($configuration['instance']), [
                    'number' => $phoneNumber,
                    'text' => $message,
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Evolution API rejected a WhatsApp message.', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Evolution API request failed.', [
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    private function request(string $apiKey): PendingRequest
    {
        return Http::withHeaders([
            'apikey' => $apiKey,
        ])->acceptJson()->timeout(10);
    }

    private function configuration(): ?array
    {
        $url = rtrim((string) config('services.evolution.url'), '/');
        $apiKey = config('services.evolution.key');
        $instance = config('services.evolution.instance');

        if ($url === '' || ! $apiKey || ! $instance) {
            Log::warning('Evolution API is not configured.');

            return null;
        }

        return ['url' => $url, 'key' => $apiKey, 'instance' => $instance];
    }

    private function phoneFromResponse(array $data): ?string
    {
        $phone = $data['instance']['owner']
            ?? $data['instance']['number']
            ?? $data['owner']
            ?? $data['number']
            ?? null;

        return $phone ? preg_replace('/@.*$/', '', (string) $phone) : null;
    }
}
<?php

namespace App\Http\Middleware;

use App\Jobs\LogSecurityEventJob;
use App\Models\SecurityEvent;
use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SecurityEventLogger
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $exception = null;

        try {
            $response = $next($request);
        } catch (\Throwable $exception) {
            $handler = app(ExceptionHandler::class);
            $response = $handler->render($request, $exception);
        }

        $statusCode = $response->getStatusCode();

        if (in_array($statusCode, [401, 403, 404], true)) {
            $this->logSecurityEvent($request, $response, $exception);
        }

        return $response;
    }

    protected function logSecurityEvent(Request $request, SymfonyResponse $response, ?\Throwable $exception): void
    {
        $userAgent = $request->userAgent() ?? '';
        $country = $this->resolveCountry($request);

        LogSecurityEventJob::dispatch([
            'user_id' => auth()->id(),
            'status_code' => $response->getStatusCode(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'country' => $country,
            'device' => $this->detectDevice($userAgent),
            'os' => $this->detectOs($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'message' => $exception?->getMessage() ?? $this->getResponseMessage($response),
            'user_agent' => $userAgent,
        ]);
    }

    protected function resolveCountry(Request $request): ?string
    {
        return $request->header('CF-IPCountry')
            ?? $request->header('X-Country-Code')
            ?? $request->header('X-Appengine-Country')
            ?? $request->server('HTTP_CF_IPCOUNTRY')
            ?? $request->server('HTTP_X_COUNTRY_CODE')
            ?? $request->server('HTTP_GEOIP_COUNTRY_CODE')
            ?? null;
    }

    protected function detectOs(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Macintosh'), str_contains($userAgent, 'Mac OS X') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad'), str_contains($userAgent, 'iPod') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            str_contains($userAgent, 'CrOS') => 'Chrome OS',
            default => 'Unknown',
        };
    }

    protected function detectDevice(string $userAgent): string
    {
        if (preg_match('/bot|crawl|slurp|spider|robot/i', $userAgent)) {
            return 'Bot';
        }

        if (preg_match('/Mobile|Android|iPhone|iPod/i', $userAgent)) {
            return 'Mobile';
        }

        if (preg_match('/Tablet|iPad|Nexus 7|Nexus 9|SM-T|Kindle/i', $userAgent)) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    protected function detectBrowser(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Microsoft Edge',
            str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera') => 'Opera',
            str_contains($userAgent, 'Chrome') && !str_contains($userAgent, 'Edg/') => 'Chrome',
            str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome') => 'Safari',
            str_contains($userAgent, 'Firefox') => 'Firefox',
            str_contains($userAgent, 'MSIE') || str_contains($userAgent, 'Trident') => 'Internet Explorer',
            default => 'Unknown',
        };
    }

    protected function getResponseMessage(SymfonyResponse $response): string
    {
        return method_exists($response, 'getStatusText')
            ? $response->getStatusText()
            : (string) $response->getContent();
    }
}

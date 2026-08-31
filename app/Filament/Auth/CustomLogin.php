<?php

namespace App\Filament\Auth;

use App\Jobs\LogSecurityEventJob;
use App\Models\SecurityEvent;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Auth\SessionGuard;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\MultiFactor\Contracts\HasBeforeChallengeHook;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;


class CustomLogin extends Login
{

       public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->logLoginAttempt($data, 'failed', 'Too many login attempts');
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        /** @var SessionGuard $authGuard */
        $authGuard = Filament::auth();

        $authProvider = $authGuard->getProvider(); /** @phpstan-ignore-line */
        $credentials = $this->getCredentialsFromFormData($data);

        $user = $authProvider->retrieveByCredentials($credentials);

        if ((! $user) || (! $authProvider->validateCredentials($user, $credentials))) {
            $this->userUndertakingMultiFactorAuthentication = null;

            $this->fireFailedEvent($authGuard, $user, $credentials);
            $this->logLoginAttempt($credentials, 'failed', 'Invalid email or password', $user);

            $this->throwFailureValidationException();
        }

        if (
            filled($this->userUndertakingMultiFactorAuthentication) &&
            (decrypt($this->userUndertakingMultiFactorAuthentication) === $user->getAuthIdentifier())
        ) {
            $this->multiFactorChallengeForm->validate();
        } else {
            foreach (Filament::getMultiFactorAuthenticationProviders() as $multiFactorAuthenticationProvider) {
                if (! $multiFactorAuthenticationProvider->isEnabled($user)) {
                    continue;
                }

                $this->userUndertakingMultiFactorAuthentication = encrypt($user->getAuthIdentifier());

                if ($multiFactorAuthenticationProvider instanceof HasBeforeChallengeHook) {
                    $multiFactorAuthenticationProvider->beforeChallenge($user);
                }

                break;
            }

            if (filled($this->userUndertakingMultiFactorAuthentication)) {
                $this->multiFactorChallengeForm->fill();

                return null;
            }
        }

        if (! $authGuard->attemptWhen($credentials, function (Authenticatable $authUser): bool {
            if (! ($authUser instanceof FilamentUser)) {
                return true;
            }

            return $authUser->canAccessPanel(Filament::getCurrentOrDefaultPanel());
        }, $data['remember'] ?? false)) {
            $this->fireFailedEvent($authGuard, $user, $credentials);
            $this->logLoginAttempt($credentials, 'failed', 'User does not have access to this panel', $user);
            $this->throwFailureValidationException();
        }

        $this->logLoginAttempt($credentials, 'success', 'Login successful', $user);

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function logLoginAttempt(array $credentials, string $status, string $message, ?Authenticatable $user = null): void
    {
        $userAgent = request()->userAgent() ?? '';

        LogSecurityEventJob::dispatch([
            'user_id' => $user?->getAuthIdentifier(),
            'event_type' => 'admin_login',
            'status' => $status,
            'status_code' => $status === 'success' ? 200 : 401,
            'email' => $credentials['email'] ?? null,
            'password_mask' => $this->maskPassword($credentials['password'] ?? null),
            'method' => request()->method(),
            'url' => request()->fullUrl(),
            'path' => request()->path(),
            'ip' => request()->ip(),
            'country' => $this->resolveCountry(request()),
            'device' => $this->detectDevice($userAgent),
            'os' => $this->detectOs($userAgent),
            'browser' => $this->detectBrowser($userAgent),
            'message' => $message,
            'user_agent' => $userAgent,
        ]);
    }

    protected function maskPassword(?string $password): ?string
    {
        if ($password === null) {
            return null;
        }

        $length = strlen($password);

        if ($length <= 2) {
            return str_repeat('*', $length);
        }

        return substr($password, 0, 1) . str_repeat('*', $length - 2) . substr($password, -1);
    }

    protected function resolveCountry($request): ?string
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
}

<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ActiveBuyersOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    protected function getHeading(): ?string
    {
        return 'Active Buyers';
    }

    protected function getDescription(): ?string
    {
        return 'Registered buyer accounts on the platform.';
    }

    protected function getStats(): array
    {
        $activeBuyers = User::query()
            ->where('role_name', 'customer')
            ->count();

        return [
            Stat::make('Active Buyers', (string) $activeBuyers)
                ->description('Total number of buyer accounts')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }
}

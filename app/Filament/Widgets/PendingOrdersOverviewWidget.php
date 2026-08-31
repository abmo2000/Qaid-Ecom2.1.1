<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PendingOrdersOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    protected function getHeading(): ?string
    {
        return 'Pending Orders';
    }

    protected function getDescription(): ?string
    {
        return 'New orders waiting for review.';
    }

    protected function getStats(): array
    {
        $pendingOrdersQuery = Order::query()
            ->where('status', OrderStatus::PENDING->value);

        $pendingOrdersCount = $pendingOrdersQuery->count();
        $pendingOrdersTotal = (float) $pendingOrdersQuery->sum('amount');

        return [
            Stat::make('Pending Orders', (string) $pendingOrdersCount)
                ->description('Orders currently marked as pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrdersCount > 0 ? 'warning' : 'gray'),

            Stat::make('Pending Value', number_format($pendingOrdersTotal, 2) . ' EGP')
                ->description('Total value of pending orders')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}

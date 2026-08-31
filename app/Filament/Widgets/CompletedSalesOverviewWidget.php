<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CompletedSalesOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    protected function getHeading(): ?string
    {
        return 'Completed Sales';
    }

    protected function getDescription(): ?string
    {
        return 'Grand total of all completed orders on site.';
    }

    protected function getStats(): array
    {
        $completedOrdersQuery = Order::query()
            ->where('status', OrderStatus::COMPLETED->value);

        $totalSales = (float) $completedOrdersQuery->sum('amount');
        $completedOrdersCount = $completedOrdersQuery->count();

        return [
            Stat::make('Total Sales', number_format($totalSales, 2) . ' EGP')
                ->description('Only completed transactions')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Completed Orders', (string) $completedOrdersCount)
                ->description('Count of completed transactions')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }
}

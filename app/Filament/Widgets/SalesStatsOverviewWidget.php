<?php

namespace App\Filament\Widgets;

use App\Enums\AdminRole;
use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\SalesAdminInvoices\SalesAdminInvoicesResource;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SalesStatsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    protected function getHeading(): ?string
    {
        return 'Sales Stats';
    }

    protected function getDescription(): ?string
    {
        return 'Invoice performance for sales admins.';
    }

    protected function getStats(): array
    {
        $salesAdminsWithInvoices = User::query()
            ->where('role_name', AdminRole::SALES_ADMIN->value)
            ->withCount(['createdOrders as invoices_count' => function ($query) {
                $query->where('status', OrderStatus::COMPLETED->value);
            }])
            ->withSum(['createdOrders as invoices_total' => function ($query) {
                $query->where('status', OrderStatus::COMPLETED->value);
            }], 'amount')
            ->get();

        $totalInvoices = (int) $salesAdminsWithInvoices->sum('invoices_count');

        /** @var User|null $bestSeller */
        $bestSeller = $salesAdminsWithInvoices->sortByDesc('invoices_count')->first();

        /** @var User|null $highestTotalSeller */
        $highestTotalSeller = $salesAdminsWithInvoices->sortByDesc(function (User $user): float {
            return (float) ($user->invoices_total ?? 0);
        })->first();

        $bestSellerName = $bestSeller?->name ?? 'N/A';
        $bestSellerInvoices = (int) ($bestSeller?->invoices_count ?? 0);
        $highestTotalName = $highestTotalSeller?->name ?? 'N/A';
        $highestTotalAmount = (float) ($highestTotalSeller?->invoices_total ?? 0);

        return [
            Stat::make('Total Invoices', (string) $totalInvoices)
                ->description('Invoices created by sales admins')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->url(SalesAdminInvoicesResource::getUrl('index')),

            Stat::make('Best Seller', $bestSellerName)
                ->description("{$bestSellerInvoices} invoices")
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->url($bestSeller ? OrderResource::getUrl('index', ['sales_admin_id' => $bestSeller->id]) : SalesAdminInvoicesResource::getUrl('index')),

            Stat::make('Highest Total', number_format($highestTotalAmount, 2) . ' EGP')
                ->description($highestTotalName)
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->url($highestTotalSeller ? OrderResource::getUrl('index', ['sales_admin_id' => $highestTotalSeller->id]) : SalesAdminInvoicesResource::getUrl('index')),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Enums\AdminRole;
use App\Enums\OrderStatus;
use App\Filament\Resources\AdminUsers\AdminUserResource;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AdminsOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getHeading(): ?string
    {
        return 'Admins Overview';
    }

    protected function getDescription(): ?string
    {
        return 'Summary and quick access to admin roles and approval status.';
    }

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    protected function getStats(): array
    {
        $registrableAdminRoles = collect(AdminRole::registrableRoles())
            ->map(fn (AdminRole $role): string => $role->value)
            ->all();

        $adminBaseQuery = User::query()->whereIn('role_name', $registrableAdminRoles);

        $registeredAdminsCount = (clone $adminBaseQuery)->count();
        $salesAdminsCount = (clone $adminBaseQuery)->where('role_name', AdminRole::SALES_ADMIN->value)->count();
        $accountingAdminsCount = (clone $adminBaseQuery)->where('role_name', AdminRole::ACCOUNTING_ADMIN->value)->count();
        $assetAdminsCount = (clone $adminBaseQuery)->where('role_name', AdminRole::ASSET_ADMIN->value)->count();
        $approvedAdminsCount = (clone $adminBaseQuery)->where('is_approved', true)->count();
        $pendingAdminsCount = (clone $adminBaseQuery)->where('is_approved', false)->count();

        $soldUnits = OrderItem::query()
            ->whereHas('order', fn ($query) => $query->where('status', OrderStatus::COMPLETED->value))
            ->sum('quantity');

        $stockUnits = Product::query()->sum('stock');

        return [
            Stat::make('Registered Admins', (string) $registeredAdminsCount)
                ->description('All admin roles except super admin')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->url($this->adminsIndexUrl()),

            Stat::make('Sales Admins', (string) $salesAdminsCount)
                ->description('Users with sales admin role')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary')
                ->url($this->adminsIndexUrl([
                    'role_name' => ['value' => AdminRole::SALES_ADMIN->value],
                ])),

            Stat::make('Accounting Admins', (string) $accountingAdminsCount)
                ->description('Users with accounting role')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning')
                ->url($this->adminsIndexUrl([
                    'role_name' => ['value' => AdminRole::ACCOUNTING_ADMIN->value],
                ])),

            Stat::make('Asset Admins', (string) $assetAdminsCount)
                ->description('Users with asset admin role')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('success')
                ->url($this->adminsIndexUrl([
                    'role_name' => ['value' => AdminRole::ASSET_ADMIN->value],
                ])),

            Stat::make('Approved Admins', (string) $approvedAdminsCount)
                ->description('Admins currently approved')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url($this->adminsIndexUrl([
                    'is_approved' => ['value' => true],
                ])),

            Stat::make('Pending Approvals', (string) $pendingAdminsCount)
                ->description('Admins waiting for approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingAdminsCount > 0 ? 'danger' : 'gray')
                ->url($this->adminsIndexUrl([
                    'is_approved' => ['value' => false],
                ])),

            Stat::make('Products Sold', number_format((int) $soldUnits))
                ->description('Completed units sold')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),

            Stat::make('Products in Stock', number_format((int) $stockUnits))
                ->description('Available stock units')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),
        ];
    }

    private function adminsIndexUrl(array $filters = []): string
    {
        $baseUrl = AdminUserResource::getUrl('index');

        if (empty($filters)) {
            return $baseUrl;
        }

        return $baseUrl . '?' . http_build_query([
            'tableFilters' => $filters,
        ]);
    }
}

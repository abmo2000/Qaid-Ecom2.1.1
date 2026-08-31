<?php

namespace App\Filament\Resources\SalesAdminInvoices;

use App\Enums\AdminRole;
use App\Enums\OrderStatus;
use App\Filament\Resources\SalesAdminInvoices\Pages\ListSalesAdminInvoices;
use App\Filament\Resources\SalesAdminInvoices\Tables\SalesAdminInvoicesTable;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalesAdminInvoicesResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Sales Invoices';

    protected static ?string $modelLabel = 'Sales Invoice Summary';

    protected static ?string $pluralModelLabel = 'Sales Invoice Summaries';

    protected static ?string $slug = 'sales-invoices';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && ($user->isSuperAdmin() || $user->hasExtraPermission('sales_invoices'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function table(Table $table): Table
    {
        return SalesAdminInvoicesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role_name', AdminRole::SALES_ADMIN->value)
            ->withCount(['createdOrders as invoices_count' => function ($query) {
                $query->where('status', OrderStatus::COMPLETED->value);
            }])
            ->withSum(['createdOrders as invoices_total' => function ($query) {
                $query->where('status', OrderStatus::COMPLETED->value);
            }], 'amount');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalesAdminInvoices::route('/'),
        ];
    }
}

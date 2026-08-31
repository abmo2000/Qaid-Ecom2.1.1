<?php

namespace App\Filament\Widgets;

use App\Enums\AdminRole;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalesAdminsInvoicesWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Sales Admins & Created Invoices')
            ->description('Each sales admin and the invoices created by them.')
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('Sales Admin')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('invoices_count')
                    ->label('Created Invoices')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('invoices_total')
                    ->label('Invoices Amount')
                    ->money('EGP')
                    ->sortable(),
            ])
            ->defaultSort('invoices_count', 'desc')
            ->paginated([5, 10, 25]);
    }

    protected function getTableQuery(): Builder
    {
        return User::query()
            ->where('role_name', AdminRole::SALES_ADMIN->value)
            ->withCount('createdOrders as invoices_count')
            ->withSum('createdOrders as invoices_total', 'amount');
    }
}

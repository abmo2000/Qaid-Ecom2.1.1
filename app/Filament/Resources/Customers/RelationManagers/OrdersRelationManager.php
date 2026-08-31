<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable(),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options(OrderStatus::toAssociativeArray())
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->sortable(),

                TextColumn::make('customer_address')
                    ->label('Address')
                    ->limit(30)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(OrderStatus::toAssociativeArray())
                    ->label('Status'),

                Filter::make('created_at')
                    ->form([
                        // Use date range filter if needed later
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}

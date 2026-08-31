<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Customer Name')
                    ->searchable()
                    ->formatStateUsing(fn ($state, Order $record): string => $record->customer?->name ?? 'Guest Customer'),
                TextColumn::make('adminCreator.name')
                                        ->label('Admin Creator')
                                        ->badge()
                                        ->color('info')
                                        ->formatStateUsing(fn (?string $state): string => $state ?: 'Website Order')
                                        ->sortable(),
                TextColumn::make('customer_type')
                    ->searchable(),
                TextColumn::make('customer_email')
                  ->label('Customer Email')
                    ->searchable()
                    ->formatStateUsing(fn ($state, Order $record): string => $record->customer?->email ?? 'No email'),
                TextColumn::make('payment_method')
                    ->searchable(),
               SelectColumn::make('status')
                  
                    ->options(OrderStatus::toAssociativeArray())
              ->selectablePlaceholder(false),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                                TextColumn::make('customer_address')
                      ->label("Customer Address")
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                 SelectFilter::make('status')
            ->options(OrderStatus::toAssociativeArray())
            ->label('Status')
            ->query(function(Builder $query, array $data): Builder {
                
                if (isset($data['value']) && $data['value'] !== '') {
                    return $query->where('status', $data['value']);
                }
                 return $query;
             }),
        
        // Filter by date range
        Filter::make('created_at')
            ->form([
                DatePicker::make('from')
                    ->label('From Date'),
                DatePicker::make('until')
                    ->label('Until Date'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['from'],
                        fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                    )
                    ->when(
                        $data['until'],
                        fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                    );
            })
            ->indicateUsing(function (array $data): array {
                $indicators = [];
                if ($data['from'] ?? null) {
                    $indicators[] = 'From: ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                }
                if ($data['until'] ?? null) {
                    $indicators[] = 'Until: ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                }
                return $indicators;
            }),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Order $record): string => route('dashboard.orders.invoice', $record))
                    ->openUrlInNewTab(),
                //EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

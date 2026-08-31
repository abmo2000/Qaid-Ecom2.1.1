<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Customer Profile')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name')
                            ->icon('heroicon-m-user')
                            ->weight('medium'),

                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->copyMessage('Email copied!'),

                        TextEntry::make('phone')
                            ->label('Phone')
                            ->icon('heroicon-m-phone')
                            ->copyable()
                            ->copyMessage('Phone copied!'),

                        TextEntry::make('city.name')
                            ->label('City')
                            ->icon('heroicon-m-map-pin'),

                        TextEntry::make('address')
                            ->label('Address')
                            ->icon('heroicon-m-home')
                            ->columnSpanFull(),

                        TextEntry::make('orders_count')
                            ->label('Total Orders')
                            ->icon('heroicon-m-shopping-bag'),

                        TextEntry::make('traffic_count')
                            ->label('Page Visits')
                            ->icon('heroicon-m-eye'),

                        TextEntry::make('last_order_at')
                            ->label('Last Order')
                            ->dateTime('M d, Y - h:i A'),

                        TextEntry::make('last_visited_at')
                            ->label('Last Visit')
                            ->dateTime('M d, Y - h:i A'),

                        TextEntry::make('recent_orders')
                            ->label('Recent Orders')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state, $record) => $record->orders()->latest('created_at')->limit(5)->get()->map(fn($order) => "#{$order->id} ({$order->status})")->implode(', '))
                            ->placeholder('No orders yet'),

                        TextEntry::make('recent_pages')
                            ->label('Recent Page Visits')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state, $record) => $record->traffic()->latest('created_at')->limit(5)->get()->pluck('path')->implode(', '))
                            ->placeholder('No page visits yet'),

                        TextEntry::make('created_at')
                            ->label('Registered At')
                            ->dateTime('M d, Y - h:i A'),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y - h:i A'),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-user-circle'),
            ]);
    }
}

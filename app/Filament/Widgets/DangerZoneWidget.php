<?php

namespace App\Filament\Widgets;

use App\Models\SecurityEvent;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class DangerZoneWidget extends TableWidget
{
    protected static ?int $sort = 90;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Danger Zone')
            ->description('Latest unauthorized and missing requests with device, IP, and country details.')
            ->query(SecurityEvent::query()->with('user')->latest('created_at'))
            ->columns([
                BadgeColumn::make('status_code')
                    ->label('Status')
                    ->colors([
                        'danger' => static fn (?int $state): bool => in_array($state, [401, 403, 404], true),
                        'warning' => static fn (?int $state): bool => $state >= 400 && $state < 500,
                        'secondary' => static fn (?int $state): bool => $state >= 500,
                    ])
                    ->sortable(),
                TextColumn::make('method')
                    ->label('Method')
                    ->sortable(),
                TextColumn::make('path')
                    ->label('Path')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('event_type')
                    ->label('Event Type')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('User')
                    ->sortable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->sortable(),
                TextColumn::make('country')
                    ->label('Country')
                    ->sortable(),
                TextColumn::make('device')
                    ->label('Device'),
                TextColumn::make('os')
                    ->label('OS'),
                TextColumn::make('browser')
                    ->label('Browser'),
                TextColumn::make('message')
                    ->label('Message')
                    ->limit(60),
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->toolbarActions([
                Action::make('clearHistory')
                    ->label('Clear History')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        SecurityEvent::query()->delete();

                        Notification::make()
                            ->title('Danger Zone history cleared')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25]);
    }
}

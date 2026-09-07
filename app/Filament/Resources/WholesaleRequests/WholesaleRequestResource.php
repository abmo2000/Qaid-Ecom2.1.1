<?php

namespace App\Filament\Resources\WholesaleRequests;

use App\Filament\Resources\WholesaleRequests\Pages\ManageWholesaleRequests;
use App\Models\User;
use App\Models\WholesaleRequest;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class WholesaleRequestResource extends Resource
{
    protected static ?string $model = WholesaleRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Wholesale Requests';

    protected static string|UnitEnum|null $navigationGroup = 'Customers';

    protected static ?string $recordTitleAttribute = 'business_name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('business_name')
                ->label('Business Name')
                ->required()
                ->maxLength(255),
            TextInput::make('phone')
                ->required()
                ->maxLength(30),
            Textarea::make('message')
                ->rows(6)
                ->maxLength(2000),
            Select::make('status')
                ->options(self::statusOptions())
                ->required(),
            TextInput::make('locale')->disabled(),
            TextInput::make('ip_address')->disabled(),
            TextInput::make('os')->disabled(),
            Textarea::make('user_agent')->disabled()->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('business_name')
            ->columns([
                TextColumn::make('business_name')
                    ->label('Business Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('os')
                    ->label('Operating System')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('message')
                    ->limit(70)
                    ->wrap(),
                SelectColumn::make('status')
                    ->options(self::statusOptions())
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date Submitted')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(self::statusOptions()),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete Wholesale Request')
                    ->modalDescription('Are you sure you want to delete this request? This cannot be undone.')
                    ->successNotificationTitle('Wholesale request deleted'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWholesaleRequests::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return static::canAccess();
    }

    public static function canDelete($record): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && (
            $user->isSuperAdmin()
            || $user->isSalesAdmin()
            || $user->hasExtraPermission('customers')
        );
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('created_at');
    }

    private static function statusOptions(): array
    {
        return [
            'new' => 'New',
            'contacted' => 'Contacted',
            'closed' => 'Closed',
        ];
    }
}
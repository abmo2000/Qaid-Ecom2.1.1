<?php

namespace App\Filament\Resources\AdminUsers;

use App\Enums\AdminRole;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\AdminUsers\Pages\ListAdminUsers;
use App\Filament\Resources\AdminUsers\Pages\CreateAdminUser;
use App\Filament\Resources\AdminUsers\Pages\EditAdminUser;

class AdminUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Admin Users';

    protected static ?string $modelLabel = 'Admin User';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->hasExtraPermission('admin_users'));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('role_name', AdminRole::toArray());
    }

    protected static array $permissionGroups = [
        'perms_catalog'  => ['products_manage', 'products_view', 'categories', 'packages', 'routines'],
        'perms_sales'    => ['orders', 'coupons', 'sales_invoices'],
        'perms_business' => ['order_settings', 'seo_settings', 'business_info'],
        'perms_content'  => ['content_management'],
        'perms_admin'    => ['admin_users'],
    ];

    public static function splitPermissions(array $extra): array
    {
        // Backward compatibility: legacy "products" maps to full product management.
        if (in_array('products', $extra, true) && ! in_array('products_manage', $extra, true)) {
            $extra[] = 'products_manage';
        }

        $result = [];
        foreach (static::$permissionGroups as $field => $perms) {
            $result[$field] = array_values(array_intersect($extra, $perms));
        }
        return $result;
    }

    public static function mergePermissions(array $data): array
    {
        $merged = [];
        foreach (array_keys(static::$permissionGroups) as $field) {
            $merged = array_merge($merged, $data[$field] ?? []);
            unset($data[$field]);
        }
        $data['extra_permissions'] = array_values(array_unique($merged));
        return $data;
    }

    protected static function permissionSections(): array
    {
        return [
            Section::make('Catalog')
                ->schema([
                    CheckboxList::make('perms_catalog')
                        ->label('')
                        ->options([
                            'products_manage' => 'Products (Edit)',
                            'products_view'   => 'Products (View Only)',
                            'categories'      => 'Categories',
                            'packages'        => 'Packages',
                            'routines'        => 'Routines',
                        ])
                        ->columns(2),
                ]),

            Section::make('Sales')
                ->schema([
                    CheckboxList::make('perms_sales')
                        ->label('')
                        ->options([
                            'orders'         => 'Orders',
                            'coupons'        => 'Coupons',
                            'sales_invoices' => 'Sales Invoices',
                        ])
                        ->columns(2),
                ]),

            Section::make('Business Settings')
                ->schema([
                    CheckboxList::make('perms_business')
                        ->label('')
                        ->options([
                            'order_settings' => 'Order Settings',
                            'seo_settings'   => 'SEO Settings',
                            'business_info'  => 'Business Info',
                        ])
                        ->columns(2),
                ]),

            Section::make('Content')
                ->schema([
                    CheckboxList::make('perms_content')
                        ->label('')
                        ->options([
                            'content_management' => 'Content Management',
                        ]),
                ]),

            Section::make('Administration')
                ->schema([
                    CheckboxList::make('perms_admin')
                        ->label('')
                        ->options([
                            'admin_users' => 'Admin Users',
                        ]),
                ]),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->helperText('Current password cannot be displayed. Enter a new one to change it.')
                    ->confirmed()
                    ->dehydrated(fn($state): bool => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->maxLength(255),

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->revealable()
                    ->dehydrated(false)
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->maxLength(255),

                Select::make('role_name')
                    ->label('Role')
                    ->options(
                        collect(AdminRole::cases())
                            ->mapWithKeys(fn(AdminRole $role) => [$role->value => $role->label()])
                            ->toArray()
                    )
                    ->required(),

                Select::make('is_approved')
                    ->label('Approval Status')
                    ->options([
                        1 => 'Approved',
                        0 => 'Pending',
                    ])
                    ->required(),

                ...static::permissionSections(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role_name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        return AdminRole::tryFrom($state)?->label() ?? ucfirst(str_replace('_', ' ', $state));
                    })
                    ->color(function (string $state): string {
                        return match (AdminRole::tryFrom($state)) {
                            AdminRole::SUPER_ADMIN => 'danger',
                            AdminRole::SALES_ADMIN => 'info',
                            AdminRole::ACCOUNTING_ADMIN => 'warning',
                            AdminRole::ASSET_ADMIN => 'success',
                            default => 'secondary',
                        };
                    }),

                IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role_name')
                    ->label('Role')
                    ->options(
                        collect(AdminRole::cases())
                            ->mapWithKeys(fn(AdminRole $role) => [$role->value => $role->label()])
                            ->toArray()
                    ),

                TernaryFilter::make('is_approved')
                    ->label('Approval Status')
                    ->placeholder('All')
                    ->trueLabel('Approved')
                    ->falseLabel('Pending'),
            ])
            ->actions([
                Action::make('managePermissions')
                    ->label('Permissions')
                    ->icon('heroicon-o-shield-check')
                    ->color('primary')
                    ->visible(fn(User $record): bool => !$record->isSuperAdmin())
                    ->fillForm(fn(User $record): array => static::splitPermissions($record->extra_permissions ?? []))
                    ->form(static::permissionSections())
                    ->action(function (array $data, User $record): void {
                        $merged = static::mergePermissions($data);
                        $record->update(['extra_permissions' => $merged['extra_permissions']]);

                        Notification::make()
                            ->title('Permissions updated successfully')
                            ->success()
                            ->send();
                    }),

                Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->form([
                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data, User $record): void {
                        $record->update(['password' => $data['new_password']]);

                        Notification::make()
                            ->title('Password reset successfully')
                            ->body("New password for {$record->email}: {$data['new_password']}")
                            ->warning()
                            ->persistent()
                            ->send();
                    }),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Admin')
                    ->modalDescription('Are you sure you want to approve this admin? They will be able to access the admin panel.')
                    ->visible(fn(User $record): bool => !$record->is_approved && !$record->isSuperAdmin())
                    ->action(function (User $record): void {
                        $record->update(['is_approved' => true]);
                        Notification::make()
                            ->title('Admin approved successfully')
                            ->success()
                            ->send();
                    }),

                Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Revoke Admin Access')
                    ->modalDescription('Are you sure you want to revoke this admin\'s access? They will no longer be able to access the admin panel.')
                    ->visible(fn(User $record): bool => $record->is_approved && !$record->isSuperAdmin())
                    ->action(function (User $record): void {
                        $record->update(['is_approved' => false]);
                        Notification::make()
                            ->title('Admin access revoked')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('approve_selected')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        $records->each(fn(User $record) => $record->update(['is_approved' => true]));
                        Notification::make()
                            ->title('Selected admins approved')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminUsers::route('/'),
            'create' => CreateAdminUser::route('/create'),
            'edit' => EditAdminUser::route('/{record}/edit'),
        ];
    }
}

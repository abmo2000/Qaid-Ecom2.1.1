<?php

namespace App\Enums;

enum AdminRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case SALES_ADMIN = 'sales_admin';
    case ACCOUNTING_ADMIN = 'accounting_admin';
    case ASSET_ADMIN = 'asset_admin';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::SALES_ADMIN => 'Sales Admin',
            self::ACCOUNTING_ADMIN => 'Accounting Admin',
            self::ASSET_ADMIN => 'Asset Admin',
        };
    }

    public static function registrableRoles(): array
    {
        return [
            self::SALES_ADMIN,
            self::ACCOUNTING_ADMIN,
            self::ASSET_ADMIN,
        ];
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toAssociativeArray(): array
    {
        return array_column(self::cases(), 'value', 'value');
    }
}

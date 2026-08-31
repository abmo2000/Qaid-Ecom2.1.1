<?php

namespace App\Filament\Resources\OrderSettings\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class OrderSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                 Section::make("Order Settings")
                   ->schema([
                       Toggle::make('has_delivery_option')->label('Discuss another option for delivery')
                          ->afterStateHydrated(function ($state, callable $set, $record) {
                          if ($record) {
                            $arTranslation = $record->translations()->where('locale', 'en')->first();
        
                             if ($arTranslation?->value) {
                                 $decodedValue = json_decode($arTranslation->value, true);
                                 $deliveryOption = $decodedValue['has_delivery_option'] ?? false;
                                 $set('has_delivery_option', $deliveryOption ?: false);
                                 } else {
                                     $set('has_delivery_option', false);
                                 }
                          }
                            }),
                       Toggle::make('allow_first_order_for_free')->label('Allow first order for free')
                       ->afterStateHydrated(function ($state, callable $set, $record) {
                          if ($record) {
                            $arTranslation = $record->translations()->where('locale', 'en')->first();
        
                             if ($arTranslation?->value) {
                                 $decodedValue = json_decode($arTranslation->value, true);
                                 $orderFree = $decodedValue['allow_first_order_for_free'] ?? false;
                                 $set('allow_first_order_for_free', $orderFree ?: false);
                                 } else {
                                     $set('allow_first_order_for_free', false);
                                 }
                          }
                            }),

                   ]),
            ]);
    }
}

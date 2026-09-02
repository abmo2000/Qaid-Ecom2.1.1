<?php

namespace App\Filament\Resources\BuisnessInfos\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;

class BuisnessInfoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                  Section::make("Buisness Info")
                ->schema([
                    TextInput::make('email')
                    ->label('Email')
                    ->required()
                    ->email()
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                          if ($record) {
                            $arTranslation = $record->translations()->where('locale', 'en')->first();
        
                             if ($arTranslation?->value) {
                                 $decodedValue = self::decodeTranslation($arTranslation);
                                 $description = $decodedValue['email'] ?? '<p></p>';
                                 $set('email', $description ?: '<p></p>');
                                 } else {
                                     $set('email', '<p></p>');
                                 }
                          }
                            })
                    ->maxLength(255),

                TextInput::make('facebook_link')
                    ->label('Facebook Link')
                    ->url()
                    ->prefix('https://')
                    ->placeholder('facebook.com/yourpage')
                     ->afterStateHydrated(function ($state, callable $set, $record) {
                          if($record){

                              $arTranslation = $record->translations()->where('locale', 'en')->first();
          
                               if ($arTranslation?->value) {
                                   $decodedValue = self::decodeTranslation($arTranslation);
                                   $description = $decodedValue['facebook_link'] ?? '';
                                   $set('facebook_link', $description ?: '');
                                   } else {
                                       $set('facebook_link', '');
                                   }
                          }
                            })
                    ->maxLength(255),

                    TextInput::make('instagram_link')
                        ->label('Instagram Link')
                        ->url()
                        ->prefix('https://')
                        ->placeholder('instagram.com/yourprofile')
                        ->afterStateHydrated(function ($state, callable $set, $record) {
                            if ($record) {
                                $arTranslation = $record->translations()->where('locale', 'en')->first();

                                if ($arTranslation?->value) {
                                    $decodedValue = self::decodeTranslation($arTranslation);
                                    $description = $decodedValue['instagram_link'] ?? '';
                                    $set('instagram_link', $description ?: '');
                                } else {
                                    $set('instagram_link', '');
                                }
                            }
                        })
                        ->maxLength(255),

                TextInput::make('telegram_link')
                    ->label('Telegram Link')
                    ->placeholder('t.me/yourchannel or @username')
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                        if ($record) {
                            $arTranslation = $record->translations()->where('locale', 'en')->first();

                            if ($arTranslation?->value) {
                                $decodedValue = self::decodeTranslation($arTranslation);
                                $description = $decodedValue['telegram_link'] ?? '';
                                $set('telegram_link', $description ?: '');
                            } else {
                                $set('telegram_link', '');
                            }
                        }
                    })
                    ->maxLength(255),

                TextInput::make('whatsapp_number')
                    ->label('WhatsApp Number')
                    ->tel()
                    ->prefix('+20')
                    ->placeholder('1234567890')
                      ->afterStateHydrated(function ($state, callable $set, $record) {
                           if($record){

                               $arTranslation = $record->translations()->where('locale', 'en')->first();
           
                                if ($arTranslation?->value) {
                                    $decodedValue = self::decodeTranslation($arTranslation);
                                    $description = $decodedValue['whatsapp_number'] ?? '<p></p>';
                                    $set('whatsapp_number', $description ?: '<p></p>');
                                    } else {
                                        $set('whatsapp_number', '<p></p>');
                                    }
                           }
                            })
                    ->maxLength(15),

                TextInput::make('mobile_number')
                    ->label('Mobile Number')
                    ->tel()
                    ->required()
                    ->prefix('+20')
                     ->afterStateHydrated(function ($state, callable $set, $record) {
                         if($record){

                             $arTranslation = $record->translations()->where('locale', 'en')->first();
         
                              if ($arTranslation?->value) {
                                  $decodedValue = self::decodeTranslation($arTranslation);
                                  $description = $decodedValue['mobile_number'] ?? '<p></p>';
                                  $set('mobile_number', $description ?: '<p></p>');
                                  } else {
                                      $set('mobile_number', '<p></p>');
                                  }
                         }
                            })
                    ->placeholder('1234567890')
                    ->maxLength(15),

                TextInput::make('instapay_account')
                    ->label('Instapay Account')
                    ->placeholder('username@instapay')
                     ->afterStateHydrated(function ($state, callable $set, $record) {
                          if($record){

                              $arTranslation = $record->translations()->where('locale', 'en')->first();
          
                               if ($arTranslation?->value) {
                                   $decodedValue = self::decodeTranslation($arTranslation);
                                   $description = $decodedValue['instapay_account'] ?? '<p></p>';
                                   $set('instapay_account', $description ?: '<p></p>');
                                   } else {
                                       $set('instapay_account', '<p></p>');
                                   }
                          }
                            })
                    ->maxLength(255),

                       
                    ])
            ]);
    }

    private static function decodeTranslation($translation): array
    {
        $value = $translation->value;

        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }

        return is_array($value) ? $value : [];
    }
}

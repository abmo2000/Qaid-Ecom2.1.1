<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductTrial;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Customer Details')
                    ->description('Edit the customer\'s personal information.')
                    ->icon('heroicon-o-user-circle')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('customer_email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('customer_phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('customer_insta_account')
                            ->label('Instapay Account')
                            ->maxLength(255)
                            ->visible(fn (): bool => auth()->user()?->isSuperAdmin()),
                        Textarea::make('customer_address')
                            ->label('Delivery Address')
                            ->rows(2),
                    ]),

                Section::make('Order Status')
                    ->description('Update order status and payment details.')
                    ->icon('heroicon-o-shopping-bag')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->options(OrderStatus::toAssociativeArray())
                            ->required(),
                        Select::make('payment_method')
                            ->options([
                                'cash_on_delivery' => 'Cash on Delivery',
                                'instapay' => 'InstaPay',
                            ])
                            ->required(),
                        Select::make('payment_status')
                            ->label('Transfer Status')
                            ->options([
                                'pending' => 'Pending review',
                                'paid' => 'Transfer confirmed',
                                'not_paid' => 'Not paid / not confirmed',
                            ])
                            ->default('not_paid')
                            ->required(),
                        FileUpload::make('payment_proof_path')
                            ->label('Transfer Proof Image')
                            ->image()
                            ->disk('public')
                            ->directory('orders/payment-proofs')
                            ->acceptedFileTypes(['image/*'])
                            ->columnSpanFull(),
                        TextInput::make('amount')
                            ->label('Grand Total (EGP)')
                            ->numeric()
                            ->required()
                            ->prefix('EGP'),
                        TextInput::make('delivery_price')
                            ->label('Delivery Fee (EGP)')
                            ->numeric()
                            ->default(0)
                            ->prefix('EGP'),
                    ]),

                Section::make('Order Items')
                    ->description('Add, edit or remove items from this order.')
                    ->icon('heroicon-o-shopping-cart')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('order_items')
                            ->label('')
                            ->schema([
                                Select::make('typeable_type')
                                    ->label('Item Type')
                                    ->options([
                                        'product' => 'Product',
                                        'package' => 'Package',
                                        'producttrial' => 'Product Trial',
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('category_id', null);
                                        $set('typeable_id', null);
                                        $set('unit_price', 0);
                                        $set('amount', 0);
                                    }),

                                Select::make('category_id')
                                    ->label('Category')
                                    ->options(fn () => Category::all()->pluck('title', 'id')->toArray())
                                    ->searchable()
                                    ->live()
                                    ->placeholder('All categories')
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('typeable_id', null);
                                        $set('unit_price', 0);
                                        $set('amount', 0);
                                    }),

                                Select::make('typeable_id')
                                    ->label('Item')
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->options(function (Get $get) {
                                        $type = $get('typeable_type');
                                        if (! $type) {
                                            return [];
                                        }

                                        $categoryId = $get('category_id');

                                        return match ($type) {
                                            'product' => Product::query()
                                                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
                                                ->get()
                                                ->pluck('name', 'id')
                                                ->toArray(),
                                            'package' => Package::all()->pluck('name', 'id')->toArray(),
                                            'producttrial' => ProductTrial::query()
                                                ->with('product')
                                                ->when($categoryId, fn ($q) => $q->whereHas('product', fn ($pq) => $pq->where('category_id', $categoryId)))
                                                ->get()
                                                ->pluck('name', 'id')
                                                ->toArray(),
                                            default => [],
                                        };
                                    })
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $type = $get('typeable_type');
                                        $id = $get('typeable_id');
                                        if (! $type || ! $id) {
                                            return;
                                        }

                                        $model = match ($type) {
                                            'product' => Product::find($id),
                                            'package' => Package::find($id),
                                            'producttrial' => ProductTrial::find($id),
                                            default => null,
                                        };

                                        if ($model) {
                                            $price = $model->price ?? 0;
                                            $set('unit_price', $price);
                                            $qty = (int) ($get('quantity') ?: 1);
                                            $set('amount', $price * $qty);
                                        }
                                    }),

                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $qty = max((int) ($get('quantity') ?: 1), 1);
                                        $unitPrice = (float) ($get('unit_price') ?: 0);
                                        $set('amount', $unitPrice * $qty);
                                    }),

                                TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('EGP')
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('amount')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('EGP')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(6)
                            ->reorderable(false)
                            ->addActionLabel('Add Item')
                            ->defaultItems(0),
                    ]),

                Section::make('Order Notes')
                    ->description('Customer notes or admin remarks.')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }
}

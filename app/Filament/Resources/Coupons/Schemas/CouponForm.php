<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;


class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Coupon Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('code', strtoupper($state)))
                            ->required(),
                        Select::make('type')
                            ->options(['fixed' => 'Fixed', 'percentage' => 'Percentage'])
                            ->default('percentage')
                            ->live()
                            ->required(),
                        TextInput::make('value')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->suffix(fn(callable $get) => $get('type') === 'percentage' ? '%' : null)
                            ->prefix(fn(callable $get) => $get('type') === 'fixed' ? '₹' : null),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->required(),
                    ]),

                Section::make('Conditions & Limits')
                    ->schema([
                        TextInput::make('minimum_order_value')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₹')
                            ->default(null),
                        TextInput::make('maximum_discount')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₹')
                            ->visible(fn(callable $get) => $get('type') === 'percentage')
                            ->default(null),
                        TextInput::make('usage_limit')
                            ->minValue(1)
                            ->numeric()
                            ->default(null),
                        TextInput::make('usage_limit_per_customer')
                            ->minValue(0)
                            ->numeric()
                            ->default(null),
                    ]),

                Section::make('Validity Period')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->native(false)
                            ->helperText('When the coupon becomes active'),
                        DateTimePicker::make('expires_at')
                            ->native(false),

                    ]),




            ]);
    }
}

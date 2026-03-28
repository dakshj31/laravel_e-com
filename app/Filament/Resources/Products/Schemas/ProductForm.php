<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Product Details')
                ->columnSpanFull()
                    ->tabs([
                        Tab::make('Basic information')
                        ->icon(Heroicon::InformationCircle)
                            ->schema([
                                Section::make('Product Details')
                                ->schema([
                                    TextInput::make('name')
                                        ->required(),
                                    TextInput::make('slug')
                                        ->unique(ignoreRecord: true)
                                        ->visible(fn(string $operation) => $operation === 'edit')
                                        ->required(),
                                    Select::make('category_id')
                                        ->relationship('category', 'name')
                                        ->preload()
                                        ->searchable()
                                        ->required()
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->required(),
                                            TextInput::make('slug')
                                                ->unique(ignoreRecord:true)
                                                ->readOnly()
                                                ->visibleOn('edit'),
                                        ]),
                                    Select::make('brand_id')
                                        ->relationship('brand', 'name')
                                        ->preload()
                                        ->searchable()
                                        ->createOptionForm([
                                            TextInput::make('name')
                                                ->required(),
                                            TextInput::make('slug')
                                            ->unique(ignoreRecord:true)
                                                ->readOnly()
                                                ->visibleOn('edit')
                                                ->required(),
                                        ])
                                        ->default(null),
                                    
                                ])->columns(2),

                                Section::make('Product Description')
                                ->schema([
                                Textarea::make('short_description')
                                    ->default(null)
                                    ->columnSpanFull(),
                                RichEditor::make('description')
                                    ->default(null)
                                    ->columnSpanFull(),

                                ])
                            ]),
                        Tab::make('Pricing & Inventory')
                        ->icon(Heroicon::CurrencyRupee)
                            ->schema([
                                Section::make('Pricing')
                                ->schema([

                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->unique(ignoreRecord:true)
                                    ->helperText('Stock Keeping Unit - unique identifier')
                                    ->required(),
                                
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Selling Price')
                                    ->prefix('₹'),

                                TextInput::make('compare_price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Original Price to show discount')
                                    ->prefix('₹'),

                                TextInput::make('cost_price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText('Cost from supplier (for profit/loss calculation)')
                                    ->prefix('₹'),
                                ])->columns(2),

                                Section::make('Inventory')
                                ->schema([

                                    Toggle::make('manage_stock')
                                    ->default(true)
                                    ->helpertext('Enable Stock management for this product')
                                    ->live(),

                                    TextInput::make('stock_quantity')
                                    ->label('Stock Quantity')
                                    ->required(fn(callable $get) => $get('manage_stock'))
                                    ->disabled(fn(callable $get) => !$get('manage_stock'))
                                    ->numeric()
                                    ->default(0),

                                    Select::make('low_stock_threshold')
                                    ->options([
                                        'in_stock' => 'In stock',
                                        'out_of_stock' => 'Out of stock',
                                        'on_backorder' => 'On backorder',
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->default('in_stock'),

                                    TextInput::make('weight')
                                    ->label('Weight(kg)')
                                    ->minValue('0')
                                    ->helperText('Used for shipping calculations')
                                    ->numeric()
                                    ->default(null),
                                    
                                    
                                ])->columns(2),
                            ]),
                        Tab::make('Tab 3')
                            ->schema([
                                // ...
                            ]),
                        ]),

                
                
                
                
                
                
                    ->default('in_stock')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('has_variants')
                    ->required(),
                
                TextInput::make('meta_title')
                    ->default(null),
                Textarea::make('meta_description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('views_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}

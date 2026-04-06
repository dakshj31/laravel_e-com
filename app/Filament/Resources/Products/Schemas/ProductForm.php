<?php

namespace App\Filament\Resources\Products\Schemas;

use Illuminate\Support\Str;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\KeyValue;

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
                                                    ->unique(ignoreRecord: true)
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
                                                    ->unique(ignoreRecord: true)
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
                                            ->unique(ignoreRecord: true)
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
                                            ->helperText('Enable Stock management for this product')
                                            ->live(),

                                        TextInput::make('stock_quantity')
                                            ->label('Stock Quantity')
                                            ->required(fn(callable $get) => $get('manage_stock'))
                                            ->disabled(fn(callable $get) => !$get('manage_stock'))
                                            ->numeric()
                                            ->default(0),

                                        TextInput::make('low_stock_threshold')
                                            ->label('Low stock Alert Threshold')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->helperText('Get notified when stock falls below this number'),

                                        Select::make('stock_status')
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


                        Tab::make('Images')
                            ->icon(Heroicon::Photo)
                            ->schema([
                                Section::make('Product Images')
                                    ->description('Upload multiple images. The first image will be the primary image.')
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label('Product Images')
                                            ->multiple()
                                            ->image()
                                            ->directory('products')
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->reorderable()
                                            ->columnSpanFull()
                                            ->helperText('You can drag and drop to reorder images')
                                            ->saveRelationshipsUsing(function ($component, $state, $record) {
                                                // delete existing image
                                                $record->images()->delete();

                                                if (is_array($state)) {
                                                    foreach ($state as $index => $imagePath) {
                                                        $record->images()->create([
                                                            'image_path' => $imagePath,
                                                            'is_primary' => $index === 0,
                                                            'sort_order' => $index
                                                        ]);
                                                    }
                                                }
                                            })
                                            ->dehydrated(false)
                                    ])
                            ]),

                        Tab::make('Product Variants')
                            ->icon(Heroicon::Cog6Tooth)
                            ->schema([
                                Toggle::make('has_variants')
                                    ->live()
                                    ->required(),
                                Section::make('Product Variants')
                                    ->description('Add variants like different sizes or colors')
                                    ->schema([
                                        Repeater::make('variants')
                                            ->relationship('variants')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->label('Variant Name')
                                                    ->placeholder('e.g., Red - Large'),
                                                KeyValue::make('options'),
                                                TextInput::make('sku')
                                                    ->label('SKU')
                                                    ->unique(ignoreRecord: true)
                                                    ->helperText('Stock Keeping Unit - unique identifier')
                                                    ->default(fn() => 'VAR-' . strtoupper(Str::random(8)))
                                                    ->required()
                                                    ->columnSpan(2),
                                                TextInput::make('price')
                                                    ->required()
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->step(0.01)
                                                    ->prefix('₹'),

                                                TextInput::make('compare_price')
                                                    ->label('Compare Price')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->step(0.01)
                                                    ->prefix('₹')
                                                    ->required(),
                                                TextInput::make('stock_quantity')
                                                    ->label('Stock')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->minValue(0)
                                                    ->required(),

                                                Select::make('stock_status')
                                                    ->options([
                                                        'in_stock' => 'In stock',
                                                        'out_of_stock' => 'Out of stock',
                                                        'on_backorder' => 'On backorder',
                                                    ])
                                                    ->native(false)
                                                    ->required()
                                                    ->default('in_stock'),

                                                Toggle::make('is_active')
                                                    ->label('Active')
                                                    ->default(true),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null)
                                            ->addActionLabel('Add Variant'),
                                    ])
                                    ->visible(fn(callable $get) => $get('has_variants'))
                                    ->columnSpanFull()

                            ]),

                        // Settings tabs

                        Tab::make('Settings')
                            ->icon(Heroicon::Cog6Tooth)
                            ->schema([
                                Section::make('Product status')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->required(),
                                        Toggle::make('is_featured')
                                            ->required(),
                                    ])
                                    ->columns(2),
                                Section::make('statistics')
                                    ->schema([
                                        Placeholder::make('views_count')
                                            ->content(fn($record) => $record?->views_count ?? 0),
                                        Placeholder::make('created_at')
                                            ->label('Created')
                                            ->content(fn($record) => $record?->created_at?->diffForHumans() ?? '-')

                                    ])
                            ]),
                        Tab::make('SEO')
                            ->icon(Heroicon::MagnifyingGlass)
                            ->schema([
                                Section::make('Search Engine Optimazation')
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->default(null),
                                        Textarea::make('meta_description')
                                            ->default(null)
                                            ->columnSpanFull(),
                                    ])

                            ]),

                    ]),

            ]);
    }
}

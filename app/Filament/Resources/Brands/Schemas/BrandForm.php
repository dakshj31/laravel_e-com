<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand Information')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                ->unique(ignoreRecord:true)
                    ->readOnly()
                    ->visibleOn('edit')
                    ->required(),
                Textarea::make('description')
                    ->rows(3)
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('logo')
                    ->disk('public')
                    ->directory('brands')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->image()   
                    ->default(null),
                TextInput::make('website')
                    ->url()
                    ->default(null),
                ]),

                Section::make('Display Settings')
                ->schema([
                    Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                    ]),
                
                
            ]);
    }
}

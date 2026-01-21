<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlantaResource\Pages;
use App\Filament\Resources\PlantaResource\RelationManagers;
use App\Models\Planta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class PlantaResource extends Resource
{
    protected static ?string $model = Planta::class;
    protected static ?string $navigationGroup = 'Mantenimiento de clientes';
    protected static ?string $navigationIcon = 'heroicon-o-building-office'; // ícono tipo "fábrica/planta"
    protected static ?string $navigationLabel = 'Plantas';
    protected static ?string $modelLabel = 'Planta';
    protected static ?string $pluralModelLabel = 'Plantas';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Datos de la planta')
            ->icon('heroicon-o-building-office')
                ->columns(3)
                ->schema([
                    TextInput::make('nombre')
                        ->required()
                        ->maxLength(255),

                    Select::make('cliente_id')
                        ->label('Cliente')
                        ->relationship('cliente', 'nombre')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default(fn (Get $get) => request()->get('cliente_id')),

                    TextInput::make('contacto')
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),

                    TextInput::make('telefono1')
                        ->tel()
                        ->maxLength(255),

                    TextInput::make('telefono2')
                        ->tel()
                        ->maxLength(255),
                ]),

            Section::make('Ubicación')
            ->icon('heroicon-o-map-pin')
                ->columns(3)
                ->schema([
                    TextInput::make('direccion')
                        ->maxLength(255),

                    Select::make('country_id')
                        ->label('País')
                        ->options(Country::all()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('province_id', null)),

                    Select::make('province_id')
                        ->label('Provincia')
                        ->options(fn (Get $get): Collection =>
                            Province::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),

                    Select::make('city_id')
                        ->label('Ciudad')
                        ->options(fn (Get $get): Collection =>
                            City::query()
                                ->where('province_id', $get('province_id'))
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make('Observaciones')
            ->icon('heroicon-o-document-text')
                ->schema([
                    Textarea::make('observaciones')
                        ->columnSpanFull(),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono2')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlantas::route('/'),
            'create' => Pages\CreatePlanta::route('/create'),
            'edit' => Pages\EditPlanta::route('/{record}/edit'),
        ];
    }
}

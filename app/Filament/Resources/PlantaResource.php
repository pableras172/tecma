<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PlantaResource\Pages\ListPlantas;
use App\Filament\Resources\PlantaResource\Pages\CreatePlanta;
use App\Filament\Resources\PlantaResource\Pages\EditPlanta;
use App\Filament\Resources\PlantaResource\Pages;
use App\Filament\Resources\PlantaResource\RelationManagers;
use App\Models\Planta;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Collection;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class PlantaResource extends Resource
{
    protected static ?string $model = Planta::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Mantenimiento de clientes';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office'; // ícono tipo "fábrica/planta"
    protected static ?string $navigationLabel = 'Plantas';
    protected static ?string $modelLabel = 'Planta';
    protected static ?string $pluralModelLabel = 'Plantas';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
{
    return $schema
        ->components([
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
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('cliente.nombre')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contacto')
                    ->searchable(),
                TextColumn::make('direccion')
                    ->searchable(),
                TextColumn::make('telefono1')
                    ->searchable(),
                TextColumn::make('telefono2')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),                
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListPlantas::route('/'),
            'create' => CreatePlanta::route('/create'),
            'edit' => EditPlanta::route('/{record}/edit'),
        ];
    }
}

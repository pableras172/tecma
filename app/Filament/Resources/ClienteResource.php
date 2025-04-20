<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use Illuminate\Support\Collection;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;
    protected static ?string $navigationGroup = 'Mantenimiento de clientes';
    protected static ?string $navigationIcon = 'heroicon-o-user-group'; // ícono tipo "cliente"
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Datos del cliente')
                    ->columns(3)
                    ->icon('heroicon-o-identification')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('logos-clientes')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->imagePreviewHeight('100'),
                        TextInput::make('nombre')
                            ->required()
                            ->label('Nombre del cliente'),

                        TextInput::make('contacto')
                            ->label('Persona de contacto')
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

                        Textarea::make('descripcion')
                            ->columnSpanFull(),
                    ]),

                Section::make('Dirección')
                    ->icon('heroicon-o-map')
                    ->columns(3)
                    ->schema([
                        Select::make('country_id')
                            ->label('País')
                            ->options(Country::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('province_id', null);
                                $set('city_id', null);
                            })
                            ->afterStateHydrated(function (Set $set, $record) {
                                if ($record && $record->ciudad) {
                                    $set('country_id', $record->ciudad->province->country_id ?? null);
                                }
                            })
                            ->required(),

                        Select::make('province_id')
                            ->label('Provincia')
                            ->options(
                                fn(Get $get): Collection =>
                                Province::query()
                                    ->where('country_id', $get('country_id'))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('city_id', null))
                            ->afterStateHydrated(function (Set $set, $record) {
                                if ($record && $record->ciudad) {
                                    $set('province_id', $record->ciudad->province_id ?? null);
                                }
                            })
                            ->required(),

                        Select::make('city_id')
                            ->label('Municipio')
                            ->options(
                                fn(Get $get): Collection =>
                                City::query()
                                    ->where('province_id', $get('province_id'))
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255),

                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40)
                    ->circular(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telefono1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlantasRelationManager::class,
        ];
    }
}

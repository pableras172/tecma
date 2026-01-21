<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Gestión de empleados';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Empleados';
    protected static ?string $modelLabel = 'Empleado';
    protected static ?string $pluralModelLabel = 'Empleados';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información personal')
                    ->columns(3)
                    ->icon('heroicon-o-identification')
                    ->schema([
                        FileUpload::make('foto')
                            ->image()
                            ->disk('public')
                            ->directory('fotos-empleados')
                            ->visibility('public')
                            ->label('Foto')
                            ->imageEditor()
                            ->imagePreviewHeight('150')
                            ->deletable(true), // Permite quitar la foto
                        TextInput::make('name')
                            ->required(),

                        TextInput::make('email')
                            ->email()
                            ->required(),

                        TextInput::make('password')
                            ->password()
                            ->hiddenOn('edit')
                            ->required(),

                        TextInput::make('dni')
                            ->maxLength(255),

                        TextInput::make('telefono')
                            ->tel()
                            ->maxLength(255),

                        DatePicker::make('fecha_nacimiento'),
                        DatePicker::make('fecha_ingreso'),

                        Select::make('categoria_profesional_id')
                            ->relationship('categoriaProfesional', 'nombre')
                            ->searchable()
                            ->preload()
                            ->label('Categoría profesional')
                            ->required()
                            ->default(fn() => request()->get('categoria_profesional_id')),

                        Select::make('departamento_id')
                            ->relationship('departamento', 'nombre')
                            ->searchable()
                            ->preload()
                            ->label('Departamento')
                            ->required()
                            ->default(fn() => request()->get('departamento_id')),
                    ]),

                Section::make('Ubicación')
                    ->icon('heroicon-o-map-pin')
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
                                if ($record && $record->city) {
                                    $set('country_id', $record->city->province->country_id ?? null);
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
                                if ($record && $record->city) {
                                    $set('province_id', $record->city->province_id ?? null);
                                }
                            })
                            ->required(),

                        Select::make('city_id')
                            ->label('Ciudad')
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
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->disk('public')
                    ->circular() // opcional: para que se vea redonda
                    ->height(40), // tamaño ajustable

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('categoriaProfesional.nombre')
                    ->label('Categoría')
                    ->sortable(),
            ])
            ->filters([])
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
            // ...
            AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

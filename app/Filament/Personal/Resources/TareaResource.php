<?php

namespace App\Filament\Personal\Resources;

use App\Filament\Personal\Resources\TareaResource\Pages;
use App\Filament\Personal\Resources\TareaResource\RelationManagers;
use App\Models\Tarea;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;

class TareaResource extends Resource
{
    protected static ?string $model = Tarea::class;

    protected static ?string $navigationGroup = 'Gestión de tareas';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('usuarios', function (Builder $query) {
                $query->where('users.id', Auth::id());
            });
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('estado', 'pendiente')
            ->whereHas(
                'usuarios',
                fn(Builder $query) =>
                $query->where('users.id', Auth::id())
            )
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $tareasPendientes = static::getModel()::query()
            ->where('estado', 'pendiente')
            ->whereHas(
                'usuarios',
                fn(Builder $query) =>
                $query->where('users.id', Auth::id())
            )
            ->count();

        return $tareasPendientes > 0 ? 'warning' : 'primary';
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Número de tareas pendientes asignadas a ti';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de la tarea')
                    ->columns(2)
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),

                        Select::make('planta_id')
                            ->label('Planta')
                            ->relationship('planta', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),

                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->required(),

                        DatePicker::make('fecha_fin')
                            ->label('Fecha de fin')
                            ->required(),

                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'progreso' => 'En progreso',
                                'finalizada' => 'Finalizada',
                            ])
                            ->required()
                            ->default('pendiente')
                            ->native(false),
                    ]),

                Section::make('Descripción')
                    ->schema([
                        Textarea::make('descripcion')
                            ->rows(4)
                            ->label('Descripción'),
                    ]),

                Section::make('Usuarios asignados')
                    ->schema([
                        Select::make('usuarios')
                            ->label('Usuarios')
                            ->relationship('usuarios', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')->label('Título')->searchable(),
                TextColumn::make('planta.nombre')->label('Planta')->sortable()->searchable(),


                SelectColumn::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'progreso' => 'En progreso',
                        'finalizada' => 'Finalizada',
                    ])
                    ->selectablePlaceholder(false)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('usuarios.name')
                    ->label('Usuarios asignados')
                    ->badge()
                    ->separator(', ') // o usa ->list() si quieres en vertical
                    ->limit(5) // muestra máximo 3, luego "y 2 más..."
                    ->tooltip(fn($record) => $record->usuarios->pluck('name')->join(', ')),
                TextColumn::make('fecha_inicio')->label('Inicio')->date('d/m/Y'),

            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'progreso' => 'En progreso',
                        'finalizada' => 'Finalizada',
                    ])
            ])
            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ListTareas::route('/'),
            'create' => Pages\CreateTarea::route('/create'),
            'edit' => Pages\EditTarea::route('/{record}/edit'),
        ];
    }
}

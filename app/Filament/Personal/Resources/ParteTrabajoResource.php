<?php

namespace App\Filament\Personal\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\Planta;
use App\Models\TipoTrabajo;
use Filament\Forms\Components\Textarea;
use App\Models\User;
use Storage;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Personal\Resources\ParteTrabajoResource\Pages\ListParteTrabajos;
use App\Filament\Personal\Resources\ParteTrabajoResource\Pages\CreateParteTrabajo;
use App\Filament\Personal\Resources\ParteTrabajoResource\Pages\EditParteTrabajo;
use App\Filament\Personal\Resources\ParteTrabajoResource\Pages\ViewParteTrabajo;
use App\Filament\Personal\Resources\ParteTrabajoResource\Pages;
use App\Filament\Personal\Resources\ParteTrabajoResource\RelationManagers;
use App\Models\ParteTrabajo;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Personal\Resources\ParteTrabajoResource\RelationManagers\LineasParteTrabajoRelationManager;
use App\Filament\Personal\Resources\ParteTrabajoResource\RelationManagers\DocRelationManager;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Forms\Actions\Action;

class ParteTrabajoResource extends Resource
{
    protected static ?string $model = ParteTrabajo::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de tareas';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Mis Partes de Trabajo';

    protected static ?string $modelLabel = 'Parte de Trabajo';

    protected static ?string $pluralModelLabel = 'Partes de Trabajo';

    /**
     * Filtrar solo los partes del usuario logueado
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_responsable_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Datos Generales')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                // Primera línea: Parte de trabajo y Datos del cliente
                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])->schema([
                                    Group::make([
                                        Section::make('Parte de trabajo')
                                            ->schema([
                                                TextInput::make('numero')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true)
                                                    ->validationMessages([
                                                        'unique' => 'Este número de parte ya existe.',
                                                    ]),
                                                DatePicker::make('fecha_parte')
                                                    ->required(),
                                                // El resto sigue igual
                                                Select::make('estado')
                                                    ->label('Estado')
                                                    ->options([
                                                        'borrador' => 'Borrador',
                                                        'cerrado' => 'Cerrado',
                                                        'facturado' => 'Facturado',
                                                    ])
                                                    ->required(),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                                    Group::make([
                                        Section::make('Datos del cliente')
                                            ->schema([
                                                Select::make('cliente_id')
                                                    ->label('Cliente')
                                                    ->relationship('cliente', 'nombre')
                                                    ->required()
                                                    ->reactive(),
                                                Select::make('planta_id')
                                                    ->label('Planta')
                                                    ->options(function (callable $get) {
                                                        $clienteId = $get('cliente_id');
                                                        if (!$clienteId) {
                                                            return [];
                                                        }
                                                        return Planta::where('cliente_id', $clienteId)
                                                            ->pluck('nombre', 'id')
                                                            ->toArray();
                                                    })
                                                    ->required()
                                                    ->reactive(),
                                                Select::make('tipo_trabajo_id')
                                                    ->label('Tipo de Trabajo')
                                                    ->options(TipoTrabajo::pluck('nombre', 'id')->toArray())
                                                    ->required()
                                                    ->reactive(),
                                            ]),
                                    ]),
                                ]),

                                // Segunda línea: Datos del motor y Trabajo realizado por
                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])->schema([
                                    Group::make([
                                        Section::make('Datos del Motor')
                                            ->schema([
                                                Grid::make(4)
                                                    ->schema([
                                                        TextInput::make('horas_motor')
                                                            ->numeric()
                                                            ->required(),
                                                        TextInput::make('arranques')
                                                            ->numeric()
                                                            ->required(),
                                                        TextInput::make('modelo')
                                                            ->maxLength(255)
                                                            ->required(),
                                                        TextInput::make('numero_motor')
                                                            ->maxLength(255)
                                                            ->required(),
                                                    ]),
                                                Textarea::make('comentarios')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                                    Group::make([
                                        Section::make('Trabajo realizado por:')
                                            ->schema([
                                                Select::make('user_responsable_id')
                                                    ->label('Responsable')
                                                    ->options(User::pluck('name', 'id')->toArray())
                                                    ->default(auth()->id())
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->required(),
                                                Textarea::make('trabajo_realizado')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                                ]),
                            ]),

                        Tab::make('Resumen')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                Section::make('Resumen del trabajo')
                                    ->description('Estos valores se calculan automáticamente desde las líneas de trabajo')
                                    ->schema([
                                        Grid::make([
                                            'default' => 4,
                                            'md' => 10,
                                        ])->schema([
                                            TextInput::make('total_horas_viaje')
                                                ->label('H.V.')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            TextInput::make('total_horas_trabajo')
                                                ->label('H.T.')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            TextInput::make('total_ht1')
                                                ->label('HT1')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            TextInput::make('total_ht2')
                                                ->label('HT2')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            TextInput::make('total_hve')
                                                ->label('HVE')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            TextInput::make('total_km')
                                                ->label('Kms')
                                                ->numeric()
                                                ->default(0)
                                                ->disabled()
                                                ->dehydrated(),
                                            TextInput::make('total_media_dieta')
                                                ->label('M/D')
                                                ->numeric()
                                                ->default(0)
                                                ->disabled()
                                                ->dehydrated(),
                                            TextInput::make('total_dieta')
                                                ->label('D/C')
                                                ->numeric()
                                                ->default(0)
                                                ->disabled()
                                                ->dehydrated(),
                                            TextInput::make('total_hotel')
                                                ->label('Hotel')
                                                ->numeric()
                                                ->default(0)
                                                ->disabled()
                                                ->dehydrated(),
                                        ]),
                                    ]),
                            ]),

                        Tab::make('Firmas')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Section::make('Firmas')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'md' => 2,
                                        ])->schema([
                                            SignaturePad::make('firma_tecnico')
                                                ->label(__('Firma del técnico'))
                                                ->dotSize(2.0)
                                                ->lineMinWidth(0.5)
                                                ->lineMaxWidth(2.5)
                                                ->throttle(16)
                                                ->minDistance(5)
                                                ->velocityFilterWeight(0.7)
                                                ->backgroundColor('rgba(255, 255, 255, 1)')
                                                ->penColor('#000')
                                                ->penColorOnDark('#000')
                                                ->clearable(true)
                                                ->undoable(false)
                                                ->confirmable()
                                                ->doneAction(fn($action) => $action->iconButton()->icon('heroicon-o-check')->color('success'))
                                                ->formatStateUsing(function ($state, $record) {
                                                    if (!$state || !$record) {
                                                        return null;
                                                    }
                                                    // Si ya es base64 o URL, devolverla tal cual
                                                    if (str_starts_with($state, 'data:image') || str_starts_with($state, 'http')) {
                                                        return $state;
                                                    }
                                                    // Convertir ruta a URL pública usando asset()
                                                    return asset('storage/' . $state);
                                                })
                                                ->dehydrateStateUsing(function ($state) {
                                                    if (!$state) {
                                                        return null;
                                                    }
                                                    // Si es base64, procesar y guardar
                                                    if (str_starts_with($state, 'data:image')) {
                                                        $base64Data = preg_replace('#^data:image/\w+;base64,#i', '', $state);
                                                        if (!is_string($base64Data) || $base64Data === '') {
                                                            return null;
                                                        }
                                                        $imageData = base64_decode($base64Data, true);
                                                        if ($imageData === false || @getimagesizefromstring($imageData) === false) {
                                                            return null;
                                                        }
                                                        $fileName = 'firmas/' . uniqid('firma_tecnico_') . '.png';
                                                        Storage::disk('public')->put($fileName, $imageData);
                                                        return $fileName;
                                                    }
                                                    // Si es URL pública, extraer solo el path
                                                    if (str_starts_with($state, 'http')) {
                                                        $path = parse_url($state, PHP_URL_PATH);
                                                        return str_replace('/storage/', '', $path);
                                                    }
                                                    return $state;
                                                }),
                                            SignaturePad::make('firma_supervisor')
                                                ->label(__('Firma del supervisor'))
                                                ->dotSize(2.0)
                                                ->lineMinWidth(0.5)
                                                ->lineMaxWidth(2.5)
                                                ->throttle(16)
                                                ->minDistance(5)
                                                ->velocityFilterWeight(0.7)
                                                ->backgroundColor('rgba(255, 255, 255, 1)')
                                                ->penColor('#000')
                                                ->penColorOnDark('#000')
                                                ->clearable(true)
                                                ->undoable(false)
                                                ->confirmable()
                                                ->doneAction(fn($action) => $action->iconButton()->icon('heroicon-o-check')->color('success'))
                                                ->formatStateUsing(function ($state, $record) {
                                                    if (!$state || !$record) {
                                                        return null;
                                                    }
                                                    if (str_starts_with($state, 'data:image') || str_starts_with($state, 'http')) {
                                                        return $state;
                                                    }
                                                    return asset('storage/' . $state);
                                                })
                                                ->dehydrateStateUsing(function ($state) {
                                                    if (!$state) {
                                                        return null;
                                                    }
                                                    if (str_starts_with($state, 'data:image')) {
                                                        $base64Data = preg_replace('#^data:image/\w+;base64,#i', '', $state);
                                                        if (!is_string($base64Data) || $base64Data === '') {
                                                            return null;
                                                        }
                                                        $imageData = base64_decode($base64Data, true);
                                                        if ($imageData === false || @getimagesizefromstring($imageData) === false) {
                                                            return null;
                                                        }
                                                        $fileName = 'firmas/' . uniqid('firma_supervisor_') . '.png';
                                                        Storage::disk('public')->put($fileName, $imageData);
                                                        return $fileName;
                                                    }
                                                    if (str_starts_with($state, 'http')) {
                                                        $path = parse_url($state, PHP_URL_PATH);
                                                        return str_replace('/storage/', '', $path);
                                                    }
                                                    return $state;
                                                }),

                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->label('Número')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fecha_parte')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('planta.nombre')
                    ->label('Planta')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tipoTrabajo.nombre')
                    ->label('Tipo de Trabajo')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'borrador' => 'gray',
                        'cerrado' => 'warning',
                        'facturado' => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'borrador' => 'Borrador',
                        'cerrado' => 'Cerrado',
                        'facturado' => 'Facturado',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('total_horas_trabajo')
                    ->label('H. Trabajo')
                    ->numeric(2)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('user_responsable_id')
                    ->label('Responsable')
                    ->relationship('creador', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'cerrado' => 'Cerrado',
                        'facturado' => 'Facturado',
                    ]),

                Filter::make('fecha_parte')
                    ->schema([
                        DatePicker::make('desde')
                            ->label('Desde'),
                        DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_parte', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_parte', '<=', $date),
                            );
                    }),
            ])
            ->searchPlaceholder('Buscar:')
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => $record->user_responsable_id === auth()->id()),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->user_responsable_id === auth()->id()),
            ])
            ->toolbarActions([
                // Sin acciones masivas para usuarios normales
            ])
            ->defaultSort('fecha_parte', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            LineasParteTrabajoRelationManager::class,
            DocRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => ListParteTrabajos::route('/'),
            'create' => CreateParteTrabajo::route('/create'),
            'edit' => EditParteTrabajo::route('/{record}/edit'),
            'view' => ViewParteTrabajo::route('/{record}'),
        ];
    }

    /**
     * Deshabilitar creación para usuarios normales
     */
    public static function canCreate(): bool
    {
        return true;
    }

    /**
     * Permitir edición solo si el usuario es el responsable
     */
    public static function canEdit($record): bool
    {
        return $record->user_responsable_id === auth()->id();
    }

    /**
     * Permitir eliminación solo si el usuario es el responsable
     */
    public static function canDelete($record): bool
    {
        return $record->user_responsable_id === auth()->id();
    }
}

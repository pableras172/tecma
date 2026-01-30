<?php

namespace App\Filament\Resources;


use App\Filament\Resources\ParteTrabajoResource\Pages;
use App\Filament\Resources\ParteTrabajoResource\RelationManagers;
use App\Filament\Resources\ParteTrabajoResource\RelationManagers\DocRelationManager;
use App\Models\ParteTrabajo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ParteTrabajoResource\RelationManagers\LineasParteTrabajoRelationManager;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Forms\Actions\Action;
use Filament\Forms\Components\Tabs;


class ParteTrabajoResource extends Resource
{
    protected static ?string $model = ParteTrabajo::class;
    protected static ?string $navigationGroup = 'Gestión de tareas';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Datos Generales')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                // Primera línea: Parte de trabajo y Datos del cliente
                                Forms\Components\Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])->schema([
                                    Forms\Components\Group::make([
                                        Forms\Components\Section::make('Parte de trabajo')
                                            ->schema([
                                                Forms\Components\TextInput::make('numero')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\DatePicker::make('fecha_parte')
                                                    ->required(),
                                                // El resto sigue igual
                                                Forms\Components\Select::make('estado')
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
                                    Forms\Components\Group::make([
                                        Forms\Components\Section::make('Datos del cliente')
                                            ->schema([
                                                Forms\Components\Select::make('cliente_id')
                                                    ->label('Cliente')
                                                    ->relationship('cliente', 'nombre')
                                                    ->required()
                                                    ->reactive(),
                                                Forms\Components\Select::make('planta_id')
                                                    ->label('Planta')
                                                    ->options(function (callable $get) {
                                                        $clienteId = $get('cliente_id');
                                                        if (!$clienteId) {
                                                            return [];
                                                        }
                                                        return \App\Models\Planta::where('cliente_id', $clienteId)
                                                            ->pluck('nombre', 'id')
                                                            ->toArray();
                                                    })
                                                    ->required()
                                                    ->reactive(),
                                                Forms\Components\Select::make('tipo_trabajo_id')
                                                    ->label('Tipo de Trabajo')
                                                    ->options(\App\Models\TipoTrabajo::pluck('nombre', 'id')->toArray())
                                                    ->required()
                                                    ->reactive(),
                                            ]),
                                    ]),
                                ]),

                                // Segunda línea: Datos del motor y Trabajo realizado por
                                Forms\Components\Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])->schema([
                                    Forms\Components\Group::make([
                                        Forms\Components\Section::make('Datos del Motor')
                                            ->schema([
                                                Forms\Components\Grid::make(4)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('horas_motor')
                                                            ->numeric()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('arranques')
                                                            ->numeric()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('modelo')
                                                            ->maxLength(255)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('numero_motor')
                                                            ->maxLength(255)
                                                            ->required(),
                                                    ]),
                                                Forms\Components\Textarea::make('comentarios')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                                    Forms\Components\Group::make([
                                        Forms\Components\Section::make('Trabajo realizado por:')
                                            ->schema([
                                                Forms\Components\Select::make('user_responsable_id')
                                                    ->label('Responsable')
                                                    ->options(\App\Models\User::pluck('name', 'id')->toArray())
                                                    ->searchable()
                                                    ->required(),
                                                Forms\Components\Textarea::make('trabajo_realizado')
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                                ]),
                            ]),

                        Tabs\Tab::make('Resumen')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                Forms\Components\Section::make('Resumen del trabajo')
                                    ->description('Estos valores se calculan automáticamente desde las líneas de trabajo')
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'default' => 4,
                                            'md' => 10,
                                        ])->schema([
                                            Forms\Components\TextInput::make('total_horas_viaje')
                                                ->label('H.V.')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('total_horas_trabajo')
                                                ->label('H.T.')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('total_ht1')
                                                ->label('HT1')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('total_ht2')
                                                ->label('HT2')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('total_hve')
                                                ->label('HVE')
                                                ->numeric()
                                                ->default(0.00)
                                                ->disabled()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('total_km')
                                                ->label('Kms')
                                                ->numeric()
                                                ->default(0)
                                                ->disabled()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('total_media_dieta')
                                                ->label('M/D')
                                                ->numeric()
                                                ->default(0)
                                                ->disabled()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('total_dieta')
                                                ->label('D/C')
                                                ->numeric()
                                                ->default(0)
                                                ->disabled()
                                                ->dehydrated(),
                                            Forms\Components\TextInput::make('total_hotel')
                                                ->label('Hotel')
                                                ->numeric()
                                                ->default(0)
                                                ->disabled()
                                                ->dehydrated(),
                                        ]),
                                    ]),
                            ]),

                        Tabs\Tab::make('Firmas')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\Section::make('Firmas')
                                    ->schema([
                                        Forms\Components\Grid::make([
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
                                                        \Storage::disk('public')->put($fileName, $imageData);
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
                                                        \Storage::disk('public')->put($fileName, $imageData);
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
                Tables\Columns\TextColumn::make('numero')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_parte')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('planta.nombre')
                    ->label('Planta')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipoTrabajo.nombre')
                    ->label('Tipo de Trabajo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('creador.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->icon(fn(string $state): ?string => match ($state) {
                        'borrador' => 'heroicon-o-pencil',
                        'cerrado' => 'heroicon-o-lock-closed',
                        'facturado' => 'heroicon-o-currency-euro',
                        default => null,
                    })
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

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('user_responsable_id')
                    ->label('Responsable')
                    ->relationship('creador', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'borrador' => 'Borrador',
                        'cerrado' => 'Cerrado',
                        'facturado' => 'Facturado',
                    ]),
            ])
            ->searchPlaceholder('Buscar:')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
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
            'index' => Pages\ListParteTrabajos::route('/'),
            'create' => Pages\CreateParteTrabajo::route('/create'),
            'edit' => Pages\EditParteTrabajo::route('/{record}/edit'),
        ];
    }
}

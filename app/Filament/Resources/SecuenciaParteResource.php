<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecuenciaParteResource\Pages;
use App\Models\SecuenciaParte;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class SecuenciaParteResource extends Resource
{
    protected static ?string $model = SecuenciaParte::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-hashtag';
    
    protected static string | \UnitEnum | null $navigationGroup = 'Configuración';
    
    protected static ?string $navigationLabel = 'Secuencias de Partes';
    
    protected static ?string $modelLabel = 'Secuencia';
    
    protected static ?string $pluralModelLabel = 'Secuencias de Partes';
    
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la Secuencia')
                    ->description('Configure el código, año y el valor inicial de la secuencia')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('codigo')
                                    ->label('Código')
                                    ->options([
                                        'TEC' => 'TEC',
                                        '2G' => '2G',
                                    ])
                                    ->required()
                                    ->disabled(fn ($record) => $record !== null)
                                    ->helperText('El código no puede modificarse una vez creado'),
                                
                                Select::make('anio')
                                    ->label('Año')
                                    ->options(function () {
                                        $currentYear = date('Y');
                                        $years = [];
                                        for ($i = -5; $i <= 5; $i++) {
                                            $year = $currentYear + $i;
                                            $years[$year] = $year;
                                        }
                                        return $years;
                                    })
                                    ->required()
                                    ->disabled(fn ($record) => $record !== null)
                                    ->helperText('El año no puede modificarse una vez creado')
                                    ->default(date('Y')),
                                
                                TextInput::make('secuencia')
                                    ->label('Secuencia Actual')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->default(0)
                                    ->helperText('El próximo parte usará este valor + 1')
                                    ->live(),
                            ]),
                    ])
                    ->columnSpanFull(),
                
                Section::make('Información')
                    ->description('Ejemplo del próximo número de parte que se generará')
                    ->schema([
                        Placeholder::make('ejemplo')
                            ->label('Próximo número')
                            ->content(function ($get, $record) {
                                $codigo = $get('codigo') ?? $record?->codigo ?? 'XXX';
                                $anio = $get('anio') ?? $record?->anio ?? date('Y');
                                $secuencia = ((int) $get('secuencia') ?? $record?->secuencia ?? 0) + 1;
                                
                                return sprintf('%s-%d-%04d', strtoupper($codigo), $anio, $secuencia);
                            })
                            ->extraAttributes(['class' => 'text-2xl font-bold text-primary-600']),
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('anio')
                    ->label('Año')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('secuencia')
                    ->label('Secuencia Actual')
                    ->sortable()
                    ->numeric()
                    ->description(fn ($record) => 'Próximo: ' . ($record->secuencia + 1)),
                
                TextColumn::make('ejemplo')
                    ->label('Próximo Número')
                    ->state(function ($record) {
                        return SecuenciaParte::generarNumero(
                            $record->codigo,
                            $record->anio,
                            $record->secuencia + 1
                        );
                    })
                    ->badge()
                    ->color('success'),
                
                TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('codigo')
                    ->label('Código')
                    ->options([
                        'TEC' => 'TEC',
                        '2G' => '2G',
                    ]),
                
                Tables\Filters\SelectFilter::make('anio')
                    ->label('Año')
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($i = -5; $i <= 5; $i++) {
                            $year = $currentYear + $i;
                            $years[$year] = $year;
                        }
                        return $years;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ])
            ->defaultSort('anio', 'desc');
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
            'index' => Pages\ListSecuenciaPartes::route('/'),
            'create' => Pages\CreateSecuenciaParte::route('/create'),
            'edit' => Pages\EditSecuenciaParte::route('/{record}/edit'),
        ];
    }
}

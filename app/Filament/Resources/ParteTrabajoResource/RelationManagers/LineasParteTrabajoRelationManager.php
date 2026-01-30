<?php

namespace App\Filament\Resources\ParteTrabajoResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\{
    DatePicker,
    TimePicker,
    TextInput,
    Toggle,
    Grid,
    Select
};

class LineasParteTrabajoRelationManager extends RelationManager
{
    protected static string $relationship = 'lineasParteTrabajo';
    protected static ?string $title = 'Registros de parte de trabajo';
    

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                \Filament\Schemas\Components\Grid::make(1)->schema([
                    DatePicker::make('fecha')
                        ->required()
                        ->default(now()),
                    
                    Select::make('usuarios')
                        ->label('Usuarios asignados')
                        ->relationship('usuarios', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText('Selecciona uno o varios usuarios para esta línea de trabajo'),
                ]),
                \Filament\Schemas\Components\Grid::make(4)->schema([
                    TimePicker::make('hora_ida')->withoutSeconds()
                        ->label('H. ida')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $this->calcularHVE($set, $get)),
                    TimePicker::make('hora_llegada')->withoutSeconds()
                        ->label('H. llegada')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $this->calcularHVE($set, $get)),
                    TimePicker::make('hora_vuelta')->withoutSeconds()
                        ->label('H. Vuelta')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $this->calcularHVE($set, $get)),
                    TimePicker::make('hora_vuelta_llegada')->withoutSeconds()
                        ->label('H. Vuelta llegada')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $this->calcularHVE($set, $get)),
                ]),

                \Filament\Schemas\Components\Grid::make(4)->schema([
                    TimePicker::make('hora_inicio_trabajo')->withoutSeconds()
                        ->label('H. inicio t.')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $this->calcularHT1($set, $get)),
                    TimePicker::make('hora_fin_trabajo')->withoutSeconds()
                        ->label('H. fin t.')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $this->calcularHT1($set, $get)),
                    TimePicker::make('hora_inicio_trabajo2')->withoutSeconds()
                        ->label('H. inicio t2.')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $this->calcularHT2($set, $get)),
                    TimePicker::make('hora_fin_trabajo2')->withoutSeconds()
                        ->label('H. fin t2.')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Set $set, Get $get) => $this->calcularHT2($set, $get)),
                ]),

                \Filament\Schemas\Components\Grid::make(5)->schema([
                    TimePicker::make('hora_entrada')
                        ->label('HE')
                        ->default(setting('horarios.hora_entrada', '08:00'))
                        ->disabled()
                        ->dehydrated(),
                    TimePicker::make('hora_salida')
                        ->label('HS')
                        ->default(setting('horarios.hora_salida', '17:00'))
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('ht1')
                        ->numeric()
                        ->label('HT1')
                        ->default('0')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('ht2')
                        ->numeric()
                        ->label('HT2')
                        ->default('0')
                        ->disabled()
                        ->dehydrated(),
                    TextInput::make('hve')
                        ->numeric()
                        ->label('HVE')
                        ->default('0')
                        ->disabled()
                        ->dehydrated(),
                ]),
              

                \Filament\Schemas\Components\Grid::make(4)->schema([
                    TextInput::make('kms')
                        ->numeric()
                        ->default('0')
                        ->disabled(fn (Get $get) => empty($get('usuarios'))),
                    Toggle::make('media_dieta')
                        ->disabled(fn (Get $get) => empty($get('usuarios'))),
                    Toggle::make('dieta_completa')
                        ->disabled(fn (Get $get) => empty($get('usuarios'))),
                    Toggle::make('hotel')
                        ->disabled(fn (Get $get) => empty($get('usuarios'))),
                ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')->date('d/m/Y')->sortable(),
                TextColumn::make('usuarios.name')
                    ->label('Usuarios')
                    ->badge()
                    ->separator(', ')
                    ->limit(3)
                    ->tooltip(fn($record) => $record->usuarios->pluck('name')->join(', ')),
                TextColumn::make('ht1')->label('HT1'),
                TextColumn::make('ht2')->label('HT2'),
                TextColumn::make('hve')->label('HVE'),
                TextColumn::make('kms')->label('Kms'),
                IconColumn::make('media_dieta')->boolean()->label('M/D'),
                IconColumn::make('dieta_completa')->boolean()->label('D/C'),
                IconColumn::make('hotel')->boolean()->label('Hotel'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->recalcularTotales();
                        $this->dispatch('reloadTotales');
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->recalcularTotales();
                        $this->dispatch('reloadTotales');
                    }),
                DeleteAction::make()
                    ->after(function () {
                        $this->getOwnerRecord()->recalcularTotales();
                        $this->dispatch('reloadTotales');
                    }),
            ])
            ->defaultSort('fecha');
    }

    /**
     * Calcula las horas extras de trabajo del primer rango (HT1)
     */
    protected function calcularHT1(Set $set, Get $get): void
    {
        $horaInicio = $get('hora_inicio_trabajo');
        $horaFin = $get('hora_fin_trabajo');
        
        if (!$horaInicio || !$horaFin) {
            $set('ht1', 0);
            return;
        }

        $horasExtra = $this->calcularHorasExtraRango($horaInicio, $horaFin);
        $set('ht1', round($horasExtra, 2));
    }

    /**
     * Calcula las horas extras de trabajo del segundo rango (HT2)
     */
    protected function calcularHT2(Set $set, Get $get): void
    {
        $horaInicio = $get('hora_inicio_trabajo2');
        $horaFin = $get('hora_fin_trabajo2');
        
        if (!$horaInicio || !$horaFin) {
            $set('ht2', 0);
            return;
        }

        $horasExtra = $this->calcularHorasExtraRango($horaInicio, $horaFin);
        $set('ht2', round($horasExtra, 2));
    }

    /**
     * Calcula las horas extras de viaje (HVE)
     */
    protected function calcularHVE(Set $set, Get $get): void
    {
        $horaIda = $get('hora_ida');
        $horaLlegada = $get('hora_llegada');
        $horaVuelta = $get('hora_vuelta');
        $horaVueltaLlegada = $get('hora_vuelta_llegada');

        $horasExtra = 0;

        // Calcular horas extra de ida
        if ($horaIda && $horaLlegada) {
            $horasExtra += $this->calcularHorasExtraRango($horaIda, $horaLlegada);
        }

        // Calcular horas extra de vuelta
        if ($horaVuelta && $horaVueltaLlegada) {
            $horasExtra += $this->calcularHorasExtraRango($horaVuelta, $horaVueltaLlegada);
        }

        $set('hve', round($horasExtra, 2));
    }

    /**
     * Calcula las horas que están fuera del rango configurado
     */
    protected function calcularHorasExtraRango(string $horaInicio, string $horaFin): float
    {
        $horaEntrada = setting('horarios.hora_entrada', '08:00');
        $horasSalida = setting('horarios.hora_salida', '17:00');

        // Convertir a objetos Carbon para facilitar los cálculos
        $inicio = Carbon::createFromFormat('H:i', $horaInicio);
        $fin = Carbon::createFromFormat('H:i', $horaFin);
        $entrada = Carbon::createFromFormat('H:i', $horaEntrada);
        $salida = Carbon::createFromFormat('H:i', $horasSalida);

        $horasExtra = 0;

        // Si el rango de trabajo termina antes de empezar, ajustar (pasa por medianoche)
        if ($fin->lt($inicio)) {
            $fin->addDay();
        }

        // Calcular horas antes de las 08:00
        if ($inicio->lt($entrada)) {
            $finAntesEntrada = $fin->lt($entrada) ? $fin : $entrada;
            $horasExtra += $inicio->diffInMinutes($finAntesEntrada) / 60;
        }

        // Calcular horas después de las 17:00
        if ($fin->gt($salida)) {
            $inicioDespuesSalida = $inicio->gt($salida) ? $inicio : $salida;
            $horasExtra += $inicioDespuesSalida->diffInMinutes($fin) / 60;
        }

        return $horasExtra;
    }

}

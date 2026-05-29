<?php

namespace App\Filament\Personal\Resources\ParteTrabajoResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                Section::make('Datos base de la línea')
                    ->description('Define la fecha, los usuarios y si el día es festivo para el cálculo.')
                    ->compact()
                    ->schema([
                        DatePicker::make('fecha')
                            ->required()
                            ->default(now()),
                        Select::make('usuarios')
                            ->label('Usuarios asignados')
                            ->relationship('usuarios', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->helperText('Selecciona uno o varios usuarios para esta línea de trabajo'),
                        Toggle::make('esfestivo')
                            ->label('Festivo')
                            ->live()
                            ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->recalcularHorasExtrasLinea($set, $get, (bool) $state))
                            ->extraAttributes(['class' => 'h-full flex items-center justify-center'])
                    ])
                    ->columns(['default' => 1, 'md' => 3])
                    ->columnSpanFull(),
                Section::make('Horario personalizado')
                    ->description('Activa esta opción para usar una hora de entrada y salida distinta a la configuración general.')
                    ->compact()
                    ->schema([
                        Toggle::make('personalizar_horario')
                            ->label('Personalizar horario')
                            ->live()
                            ->dehydrated(false)
                            ->default(fn (Get $get): bool => filled($get('hora_entrada_pers')) || filled($get('hora_salida_pers')))
                            ->afterStateHydrated(function ($state, Set $set, Get $get): void {
                                $set('personalizar_horario', filled($get('hora_entrada_pers')) || filled($get('hora_salida_pers')));
                                $this->sincronizarHorarioBase($set, $get);
                            })
                            ->afterStateUpdated(function (bool $state, Set $set, Get $get): void {
                                if (!$state) {
                                    $set('hora_entrada_pers', null);
                                    $set('hora_salida_pers', null);
                                }

                                $this->sincronizarHorarioBase($set, $get);
                            }),
                        TimePicker::make('hora_entrada_pers')
                            ->label('Entrada personalizada')
                            ->withoutSeconds()
                            ->visible(fn (Get $get): bool => (bool) $get('personalizar_horario'))
                            ->dehydrated(fn (Get $get): bool => (bool) $get('personalizar_horario'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                $this->sincronizarHorarioBase($set, $get);
                                $this->recalcularHorasExtrasLinea($set, $get);
                            }),
                        TimePicker::make('hora_salida_pers')
                            ->label('Salida personalizada')
                            ->withoutSeconds()
                            ->visible(fn (Get $get): bool => (bool) $get('personalizar_horario'))
                            ->dehydrated(fn (Get $get): bool => (bool) $get('personalizar_horario'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                $this->sincronizarHorarioBase($set, $get);
                                $this->recalcularHorasExtrasLinea($set, $get);
                            }),
                    ])
                    ->columns(['default' => 1, 'md' => 3])
                    ->columnSpanFull(),
                \Filament\Schemas\Components\Grid::make(4)->schema([
                    TimePicker::make('hora_ida')->withoutSeconds()
                        ->label('H. ida')
                        ->reactive()
                        ->disabled(fn(Get $get) => empty($get('usuarios')))
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calcularHVE($set, $get)),
                    TimePicker::make('hora_llegada')->withoutSeconds()
                        ->label('H. llegada')
                        ->reactive()
                        ->disabled(fn(Get $get) => empty($get('usuarios')))
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calcularHVE($set, $get)),
                    TimePicker::make('hora_vuelta')->withoutSeconds()
                        ->label('H. Vuelta')
                        ->reactive()
                        ->disabled(fn(Get $get) => empty($get('usuarios')))
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calcularHVE($set, $get)),
                    TimePicker::make('hora_vuelta_llegada')->withoutSeconds()
                        ->label('H. Vuelta llegada')
                        ->reactive()
                        ->disabled(fn(Get $get) => empty($get('usuarios')))
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calcularHVE($set, $get)),
                ]),

                \Filament\Schemas\Components\Grid::make(4)->schema([
                    TimePicker::make('hora_inicio_trabajo')->withoutSeconds()
                        ->label('H. inicio t.')
                        ->reactive()
                        ->disabled(fn(Get $get) => empty($get('usuarios')))
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calcularHT1($set, $get)),
                    TimePicker::make('hora_fin_trabajo')->withoutSeconds()
                        ->label('H. fin t.')
                        ->reactive()
                        ->disabled(fn(Get $get) => empty($get('usuarios')))
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calcularHT1($set, $get)),
                    TimePicker::make('hora_inicio_trabajo_2')->withoutSeconds()
                        ->label('H. inicio t2.')
                        ->reactive()
                        ->disabled(fn(Get $get) => empty($get('usuarios')))
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calcularHT2($set, $get)),
                    TimePicker::make('hora_fin_trabajo_2')->withoutSeconds()
                        ->label('H. fin t2.')
                        ->reactive()
                        ->disabled(fn(Get $get) => empty($get('usuarios')))
                        ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calcularHT2($set, $get)),
                ]),

                \Filament\Schemas\Components\Grid::make([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 3,
                    '2xl' => 5,
                ])->schema([
                    TimePicker::make('hora_entrada')
                        ->label('HE')
                        ->default(config('app.hora_entada', '08:00'))
                        ->disabled()
                        ->columnSpan(1)
                        ->dehydrated(),
                    TimePicker::make('hora_salida')
                        ->label('HS')
                        ->default(config('app.hora_salida', '17:00'))
                        ->disabled()
                        ->columnSpan(1)
                        ->dehydrated(),
                    TextInput::make('ht1')
                        ->numeric()
                        ->label('HT1')
                        ->default('0')
                        ->disabled()
                        ->columnSpan(1)
                        ->dehydrated(),
                    TextInput::make('ht2')
                        ->numeric()
                        ->label('HT2')
                        ->default('0')
                        ->disabled()
                        ->columnSpan(1)
                        ->dehydrated(),
                    TextInput::make('hve')
                        ->numeric()
                        ->label('HVE')
                        ->default('0')
                        ->disabled()
                        ->columnSpan(1)
                        ->dehydrated(),
                ]),


                \Filament\Schemas\Components\Grid::make(4)->schema([
                    TextInput::make('kms')
                        ->numeric()
                        ->default('0')
                        ->disabled(fn(Get $get) => empty($get('usuarios'))),
                ]),

                Section::make('Dietas y alojamiento')
                    ->compact()
                    ->schema([
                        Toggle::make('media_dieta')
                            ->label('Media dieta')
                            ->disabled(fn(Get $get) => empty($get('usuarios'))),
                        Toggle::make('dieta_completa')
                            ->label('Dieta completa')
                            ->disabled(fn(Get $get) => empty($get('usuarios'))),
                        Toggle::make('hotel')
                            ->label('Hotel')
                            ->disabled(fn(Get $get) => empty($get('usuarios'))),
                    ])
                    ->columns(['default' => 1, 'md' => 3])
                    ->columnSpanFull(),

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
                IconColumn::make('esfestivo')->boolean()->label('Festivo'),
                IconColumn::make('media_dieta')->boolean()->label('M/D'),
                IconColumn::make('dieta_completa')->boolean()->label('D/C'),
                IconColumn::make('hotel')->boolean()->label('Hotel'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn() => $this->getOwnerRecord()->user_responsable_id === auth()->id())
                    ->after(function () {
                        $this->getOwnerRecord()->recalcularTotales();
                        $this->dispatch('reloadTotales');
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn() => $this->getOwnerRecord()->user_responsable_id === auth()->id())
                    ->after(function () {
                        $this->getOwnerRecord()->recalcularTotales();
                        $this->dispatch('reloadTotales');
                    }),
                DeleteAction::make()
                    ->visible(fn() => $this->getOwnerRecord()->user_responsable_id === auth()->id())
                    ->after(function () {
                        $this->getOwnerRecord()->recalcularTotales();
                        $this->dispatch('reloadTotales');
                    }),
            ])
            ->defaultSort('fecha');
    }

    /**
     * Recalcula HT1 y HT2 en función de si la línea es festiva o no.
     */
    protected function calcularHT1(Set $set, Get $get): void
    {
        $this->recalcularHorasExtrasLinea($set, $get);
    }

    /**
     * Recalcula HT1 y HT2 en función de si la línea es festiva o no.
     */
    protected function calcularHT2(Set $set, Get $get): void
    {
        $this->recalcularHorasExtrasLinea($set, $get);
    }

    /**
     * Calcula HT1 para días laborables y HT2 para días festivos.
     */
    protected function recalcularHorasExtrasLinea(Set $set, Get $get, ?bool $esFestivo = null): void
    {
        $esFestivo = $esFestivo ?? (bool) $get('esfestivo');

        $intervalos = [
            [$get('hora_ida'), $get('hora_llegada')],
            [$get('hora_vuelta'), $get('hora_vuelta_llegada')],
            [$get('hora_inicio_trabajo'), $get('hora_fin_trabajo')],
            [$get('hora_inicio_trabajo_2'), $get('hora_fin_trabajo_2')],
        ];

        $totalHt1 = 0;
        $totalHt2 = 0;

        foreach ($intervalos as [$horaInicio, $horaFin]) {
            if (!$horaInicio || !$horaFin) {
                continue;
            }

            if ($esFestivo) {
                $totalHt2 += $this->calcularDuracionRango($horaInicio, $horaFin);
                continue;
            }

            $totalHt1 += $this->calcularHorasExtraRango($horaInicio, $horaFin, $get);
        }

        $set('ht1', round($totalHt1, 2));
        $set('ht2', round($totalHt2, 2));
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
            $horasExtra += $this->calcularHorasExtraRango($horaIda, $horaLlegada, $get);
        }

        // Calcular horas extra de vuelta
        if ($horaVuelta && $horaVueltaLlegada) {
            $horasExtra += $this->calcularHorasExtraRango($horaVuelta, $horaVueltaLlegada, $get);
        }

        $set('hve', round($horasExtra, 2));
    }

    /**
     * Calcula las horas que están fuera del rango configurado
     */
    protected function calcularHorasExtraRango(string $horaInicio, string $horaFin, ?Get $get = null): float
    {
        $horaEntradaPersonalizada = $get ? $get('hora_entrada_pers') : null;
        $horaSalidaPersonalizada = $get ? $get('hora_salida_pers') : null;

        $horaEntrada = $horaEntradaPersonalizada ?: setting('horarios.hora_entrada', '08:00');
        $horasSalida = $horaSalidaPersonalizada ?: setting('horarios.hora_salida', '17:00');

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

    protected function sincronizarHorarioBase(Set $set, Get $get): void
    {
        $horaEntradaDefault = setting('horarios.hora_entrada', '08:00');
        $horaSalidaDefault = setting('horarios.hora_salida', '17:00');

        $usarPersonalizado = (bool) $get('personalizar_horario');
        $horaEntradaPersonalizada = $get('hora_entrada_pers');
        $horaSalidaPersonalizada = $get('hora_salida_pers');

        $set('hora_entrada', $usarPersonalizado && filled($horaEntradaPersonalizada) ? $horaEntradaPersonalizada : $horaEntradaDefault);
        $set('hora_salida', $usarPersonalizado && filled($horaSalidaPersonalizada) ? $horaSalidaPersonalizada : $horaSalidaDefault);
    }

    /**
     * Calcula la duración total de un rango horario.
     */
    protected function calcularDuracionRango(string $horaInicio, string $horaFin): float
    {
        $inicio = Carbon::createFromFormat('H:i', $horaInicio);
        $fin = Carbon::createFromFormat('H:i', $horaFin);

        if ($fin->lt($inicio)) {
            $fin->addDay();
        }

        return $inicio->diffInMinutes($fin) / 60;
    }
}

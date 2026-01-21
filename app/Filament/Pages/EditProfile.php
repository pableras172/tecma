<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    protected static string $layout = 'filament-panels::components.layout.index';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información Personal')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nombre')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            TextInput::make('dni')
                                ->label('DNI')
                                ->maxLength(20),
                            TextInput::make('telefono')
                                ->label('Teléfono')
                                ->tel()
                                ->maxLength(20),
                            DatePicker::make('fecha_nacimiento')
                                ->label('Fecha de Nacimiento')
                                ->displayFormat('d/m/Y'),
                            DatePicker::make('fecha_ingreso')
                                ->label('Fecha de Ingreso')
                                ->displayFormat('d/m/Y')
                                ->disabled(),
                        ]),
                    ]),

                Section::make('Información Profesional')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('categoria_profesional_id')
                                ->label('Categoría Profesional')
                                ->relationship('categoriaProfesional', 'nombre')
                                ->disabled()
                                ->dehydrated(false),
                            Select::make('departamento_id')
                                ->label('Departamento')
                                ->relationship('departamento', 'nombre')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                    ]),

                Section::make('Ubicación')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('city_id')
                                ->label('Ciudad')
                                ->relationship('city', 'name')
                                ->searchable()
                                ->preload(),
                            TextInput::make('direccion')
                                ->label('Dirección')
                                ->maxLength(255)
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Foto de Perfil')
                    ->schema([
                        FileUpload::make('foto')
                            ->label('Foto')
                            ->image()
                            ->directory('fotos-empleados')
                            ->disk('public')
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),

                Section::make('Cambiar Contraseña')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('password')
                                ->label('Nueva Contraseña')
                                ->password()
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->revealable()
                                ->maxLength(255),
                            TextInput::make('password_confirmation')
                                ->label('Confirmar Contraseña')
                                ->password()
                                ->same('password')
                                ->dehydrated(false)
                                ->revealable()
                                ->maxLength(255),
                        ]),
                    ]),
            ]);
    }
}

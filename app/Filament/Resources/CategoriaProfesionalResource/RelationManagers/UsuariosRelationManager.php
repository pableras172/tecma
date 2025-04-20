<?php

namespace App\Filament\Resources\CategoriaProfesionalResource\RelationManagers;

use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class UsuariosRelationManager extends RelationManager
{
    protected static string $relationship = 'usuarios';
    protected static ?string $title = '🎓 Empleados con esta categoría';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(fn($record) => route('filament.dashboard.resources.users.edit', ['record' => $record]))
            ->columns([
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('departamento.nombre')->label('Departamento'),
                TextColumn::make('created_at')->label('Alta')->date(),
            ])
            ->headerActions([
                Action::make('crear')
                    ->label('Crear empleado')
                    ->icon('heroicon-o-plus')
                    ->url(fn($livewire) => route('filament.dashboard.resources.users.create', [
                        'categoria_profesional_id' => $livewire->getOwnerRecord()->id,
                    ]))
                    ,
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detalle del empleado'),

                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => route('filament.dashboard.resources.users.edit', ['record' => $record]))
                    ,
            ]);
    }
}

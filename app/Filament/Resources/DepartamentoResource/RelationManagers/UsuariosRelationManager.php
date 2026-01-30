<?php
namespace App\Filament\Resources\DepartamentoResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;

class UsuariosRelationManager extends RelationManager
{
    protected static string $relationship = 'usuarios';

    protected static ?string $title = '🏢 Empleados del departamento';

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => route('filament.dashboard.resources.users.edit', ['record' => $record]))
            ->columns([
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('categoriaProfesional.nombre')->label('Categoría'),
                TextColumn::make('created_at')->label('Alta')->date(),
            ])
            ->headerActions([
                Action::make('crear')
                    ->label('Crear empleado')
                    ->icon('heroicon-o-plus')
                    ->url(fn ($livewire) => route('filament.dashboard.resources.users.create', [
                        'departamento_id' => $livewire->getOwnerRecord()->id,
                    ]))
                    ,
            ])
            ->recordActions([
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => route('filament.dashboard.resources.users.edit', ['record' => $record]))
                    ,
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}

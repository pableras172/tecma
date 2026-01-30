<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class PlantasRelationManager extends RelationManager
{
    protected static string $relationship = 'Plantas';
    protected static ?string $title = 'Plantas asociadas';


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Plantas')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre'),
                TextColumn::make('direccion')->label('Dirección'),
                TextColumn::make('telefono1')->label('Teléfono 1'),
                TextColumn::make('telefono2')->label('Teléfono 2'),
                TextColumn::make('contacto')->label('Contacto'),
                TextColumn::make('email')->label('Email'),
            ])
            ->recordUrl(fn ($record) => route('filament.dashboard.resources.plantas.edit', ['record' => $record]))

            ->filters([])
            ->headerActions([
                Action::make('crear')
                ->label('Crear planta')
                ->icon('heroicon-o-plus')
                ->url(fn ($livewire) => route('filament.dashboard.resources.plantas.create', [
                    'cliente_id' => $livewire->getOwnerRecord()->id,
                ])),
            ])
            ->recordActions([
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => route('filament.dashboard.resources.plantas.edit', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    
}

<?php

namespace App\Filament\Resources\ClienteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Plantas')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Tables\Table $table): Tables\Table
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
                Tables\Actions\Action::make('crear')
                ->label('Crear planta')
                ->icon('heroicon-o-plus')
                ->url(fn ($livewire) => route('filament.dashboard.resources.plantas.create', [
                    'cliente_id' => $livewire->getOwnerRecord()->id,
                ])),
            ])
            ->actions([
                Tables\Actions\Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn($record) => route('filament.dashboard.resources.plantas.edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    
}

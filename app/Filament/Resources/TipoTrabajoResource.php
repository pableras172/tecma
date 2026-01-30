<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\TipoTrabajoResource\Pages\ListTipoTrabajos;
use App\Filament\Resources\TipoTrabajoResource\Pages\CreateTipoTrabajo;
use App\Filament\Resources\TipoTrabajoResource\Pages\EditTipoTrabajo;
use App\Filament\Resources\TipoTrabajoResource\Pages;
use App\Filament\Resources\TipoTrabajoResource\RelationManagers;
use App\Models\TipoTrabajo;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TipoTrabajoResource extends Resource
{
    protected static ?string $model = TipoTrabajo::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static string | \UnitEnum | null $navigationGroup = 'Configuración Tec-Ma';
    protected static ?string $navigationLabel = 'Tipos de trabajo';
    protected static ?string $pluralModelLabel = 'Tipos de trabajo';
    protected static ?string $modelLabel = 'Tipo de trabajo';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->columnSpanFull(),
                Toggle::make('activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                IconColumn::make('activo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListTipoTrabajos::route('/'),
            'create' => CreateTipoTrabajo::route('/create'),
            'edit' => EditTipoTrabajo::route('/{record}/edit'),
        ];
    }
}

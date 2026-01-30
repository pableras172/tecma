<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CategoriaProfesionalResource\RelationManagers\UsuariosRelationManager;
use App\Filament\Resources\CategoriaProfesionalResource\Pages\ListCategoriaProfesionals;
use App\Filament\Resources\CategoriaProfesionalResource\Pages\CreateCategoriaProfesional;
use App\Filament\Resources\CategoriaProfesionalResource\Pages\EditCategoriaProfesional;
use App\Filament\Resources\CategoriaProfesionalResource\Pages;
use App\Filament\Resources\CategoriaProfesionalResource\RelationManagers;
use App\Models\CategoriaProfesional;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriaProfesionalResource extends Resource
{
    protected static ?string $model = CategoriaProfesional::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de empleados';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Categorías profesionales';
    protected static ?string $modelLabel = 'Categoría profesional';
    protected static ?string $pluralModelLabel = 'Categorías profesionales';

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
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
            UsuariosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoriaProfesionals::route('/'),
            'create' => CreateCategoriaProfesional::route('/create'),
            'edit' => EditCategoriaProfesional::route('/{record}/edit'),
        ];
    }
}

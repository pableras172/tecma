<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\DocResource\Pages\ListDocs;
use App\Filament\Resources\DocResource\Pages\CreateDoc;
use App\Filament\Resources\DocResource\Pages\EditDoc;
use App\Filament\Resources\DocResource\Pages;
use App\Filament\Resources\DocResource\RelationManagers;
use App\Models\Doc;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocResource extends Resource
{
    protected static ?string $model = Doc::class;
    
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parte_trabajo_id')
                    ->label('Parte de Trabajo')
                    ->relationship('parteTrabajo', 'numero')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('ruta')
                    ->label('Subir Documento')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory(fn () => 'documentos/' . date('Y'))
                    ->disk('public')
                    ->maxSize(10240)
                    ->required()                                      
                    ->preserveFilenames()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            // Si es un objeto TemporaryUploadedFile
                            if (is_object($state)) {
                                $nombreArchivo = pathinfo($state->getClientOriginalName(), PATHINFO_FILENAME);
                            } else {
                                // Si es una string (ruta)
                                $nombreArchivo = pathinfo($state, PATHINFO_FILENAME);
                            }
                            
                            // Autocompletar nombre del documento
                            $set('nombre_documento', $nombreArchivo);
                            
                            // Autocompletar fecha con la fecha actual
                            $set('fecha', date('Y-m-d'));
                        }
                    })
                    ->columnSpanFull()
                    ,

                TextInput::make('nombre_documento')
                    ->label('Nombre del Documento')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255),

                DatePicker::make('fecha')
                    ->label('Fecha')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->displayFormat('d/m/Y'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parteTrabajo.numero')
                    ->label('Parte de Trabajo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre_documento')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('ruta')
                    ->label('Archivo')
                    ->formatStateUsing(fn ($state) => basename($state))
                    ->url(fn ($record) => asset('storage/' . $record->ruta))
                    ->openUrlInNewTab(),                
            ])
            ->filters([
                SelectFilter::make('parte_trabajo_id')
                    ->label('Parte de Trabajo')
                    ->relationship('parteTrabajo', 'numero')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => asset('storage/' . $record->ruta))
                    ->openUrlInNewTab(),
                
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListDocs::route('/'),
            'create' => CreateDoc::route('/create'),
            'edit' => EditDoc::route('/{record}/edit'),
        ];
    }
}

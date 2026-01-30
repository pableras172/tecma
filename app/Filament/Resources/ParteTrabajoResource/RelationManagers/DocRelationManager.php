<?php

namespace App\Filament\Resources\ParteTrabajoResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;

class DocRelationManager extends RelationManager
{
    protected static string $relationship = 'docs';
    
    protected static ?string $title = 'Informes';
    protected static ?string $modelLabel = 'Informe';
    protected static ?string $pluralModelLabel = 'Informes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->columnSpanFull(),

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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre_documento')
            ->columns([
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
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Subir Documento'),
            ])
            ->recordActions([
                Action::make('ver')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => $record->nombre_documento)
                    ->modalWidth('7xl')
                    ->modalContent(fn ($record) => view('filament.pages.pdf-viewer', [
                        'fileUrl' => asset('storage/' . $record->ruta)
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
                Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => asset('storage/' . $record->ruta))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

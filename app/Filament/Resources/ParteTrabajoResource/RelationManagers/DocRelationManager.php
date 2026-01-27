<?php

namespace App\Filament\Resources\ParteTrabajoResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('ruta')
                    ->label('Subir Documento')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory(fn () => 'documentos/' . date('Y'))
                    ->disk('public')
                    ->maxSize(10240)
                    ->required()
                    ->preserveFilenames()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
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

                Forms\Components\TextInput::make('nombre_documento')
                    ->label('Nombre del Documento')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('fecha')
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
                Tables\Columns\TextColumn::make('nombre_documento')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ruta')
                    ->label('Archivo')
                    ->formatStateUsing(fn ($state) => basename($state))
                    ->url(fn ($record) => asset('storage/' . $record->ruta))
                    ->openUrlInNewTab(),
               
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Subir Documento'),
            ])
            ->actions([
                Tables\Actions\Action::make('ver')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn ($record) => $record->nombre_documento)
                    ->modalWidth('7xl')
                    ->modalContent(fn ($record) => view('filament.pages.pdf-viewer', [
                        'fileUrl' => asset('storage/' . $record->ruta)
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
                Tables\Actions\Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => asset('storage/' . $record->ruta))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

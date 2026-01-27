<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocResource\Pages;
use App\Filament\Resources\DocResource\RelationManagers;
use App\Models\Doc;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocResource extends Resource
{
    protected static ?string $model = Doc::class;
    
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parte_trabajo_id')
                    ->label('Parte de Trabajo')
                    ->relationship('parteTrabajo', 'numero')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

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
                    ->columnSpanFull()
                    ,

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parteTrabajo.numero')
                    ->label('Parte de Trabajo')
                    ->searchable()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('parte_trabajo_id')
                    ->label('Parte de Trabajo')
                    ->relationship('parteTrabajo', 'numero')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => asset('storage/' . $record->ruta))
                    ->openUrlInNewTab(),
                
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDocs::route('/'),
            'create' => Pages\CreateDoc::route('/create'),
            'edit' => Pages\EditDoc::route('/{record}/edit'),
        ];
    }
}

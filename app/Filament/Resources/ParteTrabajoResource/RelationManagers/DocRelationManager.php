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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;

class DocRelationManager extends RelationManager
{
    protected static string $relationship = 'docs';

    protected static ?string $title = 'Informes';
    protected static ?string $modelLabel = 'Informe';
    protected static ?string $pluralModelLabel = 'Informes';

    protected $listeners = ['refresh-docs' => '$refresh'];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('ruta')
                    ->label('Subir Documento')
                    ->acceptedFileTypes(['application/pdf'])
                    ->directory(function () {
                        $parte = $this->getOwnerRecord();

                        $nombreCliente = \Str::slug($parte->cliente?->nombre ?? 'sin-cliente');
                        $anio = date('Y');
                        $numeroParte = $parte->numero;

                        return "documentos/{$nombreCliente}/{$anio}/{$numeroParte}";
                    })
                    ->disk('public')
                    ->maxSize(10240)
                    ->required()
                    ->preserveFilenames()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            // Obtener nombre del archivo
                            $nombreArchivo = '';

                            if (is_object($state)) {
                                $nombreArchivo = pathinfo($state->getClientOriginalName(), PATHINFO_FILENAME);
                            } else {
                                $nombreArchivo = pathinfo($state, PATHINFO_FILENAME);
                            }

                            // Autocompletar nombre del documento
                            $set('nombre_documento', 'informe_' . $nombreArchivo);

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
                    ->formatStateUsing(fn($state) => basename($state))
                    ->url(fn($record) => asset('storage/' . $record->ruta))
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
                    ->modalHeading(fn($record) => $record->nombre_documento)
                    ->modalWidth('7xl')
                    ->modalContent(fn($record) => view('filament.pages.pdf-viewer', [
                        'fileUrl' => asset('storage/' . $record->ruta)
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
                Action::make('descargar')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => asset('storage/' . $record->ruta))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('enviar_email')
                        ->label('Enviar por email al cliente')
                        ->icon('heroicon-o-envelope')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Enviar documentos por email')
                        ->modalDescription(function () {
                            $cliente = $this->getOwnerRecord()->cliente;
                            return 'Se enviarán los documentos seleccionados al cliente: ' . 
                                   $cliente?->nombre . 
                                   ($cliente?->email ? ' (' . $cliente->email . ')' : ' (sin email)');
                        })
                        ->modalSubmitActionLabel('Enviar')
                        ->action(function (Collection $records) {
                            $parte = $this->getOwnerRecord();
                            $cliente = $parte->cliente;

                            // Validar que el cliente tenga email
                            if (!$cliente || !$cliente->email) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error al enviar')
                                    ->danger()
                                    ->body('El cliente no tiene un email registrado.')
                                    ->send();
                                return;
                            }

                            try {
                                // Enviar email
                                Mail::send('emails.documentos-parte', [
                                    'cliente' => $cliente,
                                    'parte' => $parte,
                                    'documentos' => $records,
                                ], function ($message) use ($cliente, $records, $parte) {
                                    $message->to($cliente->email)
                                        ->subject("Documentos del parte de trabajo #{$parte->numero}");

                                    // Adjuntar los PDFs
                                    foreach ($records as $doc) {
                                        $filePath = storage_path('app/public/' . $doc->ruta);
                                        if (file_exists($filePath)) {
                                            $message->attach($filePath, [
                                                'as' => basename($doc->ruta),
                                                'mime' => 'application/pdf',
                                            ]);
                                        }
                                    }
                                });

                                \Filament\Notifications\Notification::make()
                                    ->title('Email enviado correctamente')
                                    ->success()
                                    ->body("Se han enviado {$records->count()} documento(s) a {$cliente->email}")
                                    ->send();
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error al enviar email')
                                    ->danger()
                                    ->body('Error: ' . $e->getMessage())
                                    ->send();
                            }
                        }),

                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc');
    }
}

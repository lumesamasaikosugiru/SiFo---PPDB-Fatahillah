<?php

namespace App\Filament\Resources\Siswas\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DokumensRelationManager extends RelationManager
{
    protected static string $relationship = 'dokumens';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pilih dokumen')
                    ->schema([
                        FileUpload::make('file_path')
                            // ->imagePreviewHeight('200')
                            ->label('Dokumen')
                            ->belowContent('Seret atau pilih dokumen dari perangkat Anda!')
                            ->disk('public')
                            ->directory('dokumen')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/jpg',
                                'application/pdf',
                            ])
                            // ->visibility('public')
                            // ->previewable()
                            // ->downloadable()
                            // ->openable()
                            ->required(fn($operation) => $operation === 'create'),

                    ])
                    ->iconColor('success')
                    ->icon(Heroicon::OutlinedDocumentCheck)
                    ->columns(1)
                    ->columnSpan(1),

                Section::make('Informasi Dokumen di Pendaftaran ini')
                    ->schema([
                        Select::make('pendaftaran_id')
                            ->relationship('pendaftaran', 'kode_regis')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(null),
                        Select::make('tipe_dokumen')
                            ->options([
                                'foto' => 'Foto',
                                'kk' => 'Kartu Keluarga',
                                'akta' => 'Akta Kelahiran',
                                'ijazah' => 'Ijazah / SKL',
                            ])
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ])
                    ->iconColor('success')
                    ->icon(Heroicon::OutlinedInformationCircle)
                    ->columns(1)
                    ->columnSpan(1),
            ])
            ->columns(2);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([

                Fieldset::make('Preview Dokumen')
                    ->schema([

                        ImageEntry::make('file_path')
                            ->label('Preview')
                            ->disk('public')
                            ->imageSize(180)
                            ->visible(
                                fn($record) =>
                                str_contains($record->file_path, '.jpg') ||
                                str_contains($record->file_path, '.jpeg') ||
                                str_contains($record->file_path, '.png') ||
                                str_contains($record->file_path, '.webp')
                            )
                            ->extraImgAttributes([
                                'class' => 'rounded-lg object-contain'
                            ]),

                        TextEntry::make('file_path')
                            ->label('Dokumen')
                            ->badge()

                            ->formatStateUsing(
                                fn($state) =>
                                '<a href="' . asset('storage/' . $state) . '" target="_blank" class="text-primary font-semibold">Buka Dokumen</a>'
                            )
                            ->html(),

                    ])
                    ->columnSpan(2),

                Group::make()
                    ->schema([

                        Fieldset::make('Informasi Dokumen')
                            ->schema([
                                TextEntry::make('tipe_dokumen')
                                    ->label('Jenis Dokumen')
                                    ->color(fn($state) => match ($state) {
                                        'kk' => 'info',
                                        'foto' => 'danger',
                                        'akta' => 'warning',
                                        'ijazah' => 'succes',
                                    })
                                    ->badge(),

                                TextEntry::make('pendaftaran.kode_regis')
                                    ->label('Kode Registrasi')
                                    ->placeholder('-'),
                            ]),

                        Fieldset::make('Waktu Upload')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label('Diperbarui')
                                    ->dateTime('d M Y H:i')
                                    ->placeholder('-'),
                            ]),

                    ])
                    ->columnSpan(2),

            ])
            ->columns(4);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tipe_dokumen')
            ->columns([
                TextColumn::make('pendaftaran.kode_regis')
                    ->searchable(),
                TextColumn::make('tipe_dokumen')
                    ->label('Tipe Dokumen')
                    ->color(fn($state) => match ($state) {
                        'kk' => 'info',
                        'foto' => 'danger',
                        'akta' => 'warning',
                        'ijazah' => 'succes',
                    })
                    ->badge()
                    ->searchable(),
                IconColumn::make('file_path')
                    ->icon(fn($record) => str_contains($record->file_path, '.pdf') ? Heroicon::DocumentText : Heroicon::Photo)
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab()
                    ->tooltip('Lihat Dokumen')
                    ->color(Color::Green)
                    ->label('Dokumen'),
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
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalHeading('Preview Dokumen')
                        ->modalCancelActionLabel('Tutup')
                        ->modalWidth('4xl')
                        ->modalContent(content: function ($record) {
                            return view('filament.preview-dokumen', ['record' => $record]);
                        })
                        ->modalSubmitAction(false),
                    EditAction::make(),
                    DissociateAction::make(),
                    DeleteAction::make(),

                ])->label('Aksi')
                    ->icon(Heroicon::PencilSquare),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

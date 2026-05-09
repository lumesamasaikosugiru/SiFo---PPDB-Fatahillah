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
use Filament\Tables\Columns\ImageColumn;
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
                                'pas_foto'       => 'Pas Foto',
                                'kk'             => 'Kartu Keluarga',
                                'akta'           => 'Akta Kelahiran',
                                'ijazah'         => 'Ijazah / SKL',
                                'skhun'          => 'SKHUN',
                                'stl'            => 'STL',
                                'lampiran_jalur' => 'Lampiran Jalur',
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
                                        'pas_foto'       => 'danger',
                                        'kk'             => 'info',
                                        'akta'           => 'warning',
                                        'ijazah'         => 'success',
                                        'skhun'          => 'gray',
                                        'stl'            => 'gray',
                                        'lampiran_jalur' => 'primary',
                                        default          => 'gray',
                                    })
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'pas_foto'       => 'Pas Foto',
                                        'kk'             => 'Kartu Keluarga',
                                        'akta'           => 'Akta Kelahiran',
                                        'ijazah'         => 'Ijazah / SKL',
                                        'skhun'          => 'SKHUN',
                                        'stl'            => 'STL',
                                        'lampiran_jalur' => 'Lampiran Jalur',
                                        default          => ucfirst(str_replace('_', ' ', $state)),
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
                        'pas_foto'       => 'danger',
                        'kk'             => 'info',
                        'akta'           => 'warning',
                        'ijazah'         => 'success',
                        'skhun'          => 'gray',
                        'stl'            => 'gray',
                        'lampiran_jalur' => 'primary',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pas_foto'       => 'Pas Foto',
                        'kk'             => 'Kartu Keluarga',
                        'akta'           => 'Akta Kelahiran',
                        'ijazah'         => 'Ijazah / SKL',
                        'skhun'          => 'SKHUN',
                        'stl'            => 'STL',
                        'lampiran_jalur' => 'Lampiran Jalur',
                        default          => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->badge()
                    ->searchable(),
                // Kolom preview inline: gambar = thumbnail, PDF = embed mini
                TextColumn::make('file_path')
                    ->label('Preview')
                    ->html()
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        $url = asset('storage/' . $state);
                        $ext = strtolower(pathinfo($state, PATHINFO_EXTENSION));

                        if ($ext === 'pdf') {
                            return '<div style="width:80px;text-align:center">'
                                . '<a href="' . $url . '" target="_blank" title="Buka PDF">'
                                . '<embed src="' . $url . '#page=1&toolbar=0&navpanes=0&scrollbar=0"'
                                . ' type="application/pdf"'
                                . ' style="width:80px;height:100px;border:1px solid #e5e7eb;border-radius:6px;pointer-events:none;"'
                                . '></embed>'
                                . '<div style="font-size:10px;color:#ef4444;margin-top:3px;font-weight:600">PDF</div>'
                                . '</a></div>';
                        }

                        return '<a href="' . $url . '" target="_blank" title="Buka gambar">'
                            . '<img src="' . $url . '"'
                            . ' style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #e5e7eb;display:block;"'
                            . ' loading="lazy"'
                            . '>'
                            . '</a>';
                    })
                    ->extraAttributes(['style' => 'padding:4px 8px']),
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

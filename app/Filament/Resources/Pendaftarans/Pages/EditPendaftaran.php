<?php

namespace App\Filament\Resources\Pendaftarans\Pages;

use App\Filament\Resources\Pendaftarans\PendaftaranResource;
use App\Models\Siswa;
use App\Models\WaliSiswa;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPendaftaran extends EditRecord
{
    protected static string $resource = PendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $pendaftaran = $this->record;

        // Update atau create siswa
        if (!empty($data['siswa'])) {
            Siswa::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                array_filter($data['siswa'], fn($v) => $v !== null)
            );
        }

        // Update atau create wali siswa
        if (!empty($data['waliSiswa'])) {
            WaliSiswa::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                array_filter($data['waliSiswa'], fn($v) => $v !== null)
            );
        }
    }
}

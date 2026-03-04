<?php

namespace App\Filament\Resources\Pendaftarans;

use App\Filament\Resources\Pendaftarans\Pages\CreatePendaftaran;
use App\Filament\Resources\Pendaftarans\Pages\EditPendaftaran;
use App\Filament\Resources\Pendaftarans\Pages\ListPendaftarans;
use App\Filament\Resources\Pendaftarans\Pages\ViewPendaftaran;
use App\Filament\Resources\Pendaftarans\Schemas\PendaftaranForm;
use App\Filament\Resources\Pendaftarans\Schemas\PendaftaranInfolist;
use App\Filament\Resources\Pendaftarans\Tables\PendaftaransTable;
use App\Models\Pendaftaran;
use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PendaftaranResource extends Resource
{
    protected static ?string $model = Pendaftaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'kode_regis';

    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Pendaftaran';
    protected static string|UnitEnum|null $navigationGroup = 'Registrations';

    public static function form(Schema $schema): Schema
    {
        return PendaftaranForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PendaftaranInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PendaftaransTable::configure($table);
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
            'index' => ListPendaftarans::route('/'),
            'create' => CreatePendaftaran::route('/create'),
            'view' => ViewPendaftaran::route('/{record}'),
            'edit' => EditPendaftaran::route('/{record}/edit'),
        ];
    }


    public static function canCreate(): bool
    {
        return auth()->user()->can('pendaftaran.create');
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()->can('pendaftaran.view');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('pendaftaran.update_status');
    }

}

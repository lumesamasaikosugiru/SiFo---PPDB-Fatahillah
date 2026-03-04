<?php

namespace App\Filament\Resources\MetodePembayarans;

use App\Filament\Resources\MetodePembayarans\Pages\CreateMetodePembayaran;
use App\Filament\Resources\MetodePembayarans\Pages\EditMetodePembayaran;
use App\Filament\Resources\MetodePembayarans\Pages\ListMetodePembayarans;
use App\Filament\Resources\MetodePembayarans\Pages\ViewMetodePembayaran;
use App\Filament\Resources\MetodePembayarans\Schemas\MetodePembayaranForm;
use App\Filament\Resources\MetodePembayarans\Schemas\MetodePembayaranInfolist;
use App\Filament\Resources\MetodePembayarans\Tables\MetodePembayaransTable;
use App\Models\MetodePembayaran;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MetodePembayaranResource extends Resource
{
    protected static ?string $model = MetodePembayaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_metode';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Metode Pembayaran';
    protected static string|UnitEnum|null $navigationGroup = 'Payments';

    public static function form(Schema $schema): Schema
    {
        return MetodePembayaranForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MetodePembayaranInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MetodePembayaransTable::configure($table);
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
            'index' => ListMetodePembayarans::route('/'),
            'create' => CreateMetodePembayaran::route('/create'),
            'view' => ViewMetodePembayaran::route('/{record}'),
            'edit' => EditMetodePembayaran::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole([
            'superadmin',
            'admin_yayasan',
        ]);
    }
}

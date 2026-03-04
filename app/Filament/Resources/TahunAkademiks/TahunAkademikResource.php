<?php

namespace App\Filament\Resources\TahunAkademiks;

use App\Filament\Resources\TahunAkademiks\Pages\CreateTahunAkademik;
use App\Filament\Resources\TahunAkademiks\Pages\EditTahunAkademik;
use App\Filament\Resources\TahunAkademiks\Pages\ListTahunAkademiks;
use App\Filament\Resources\TahunAkademiks\Pages\ViewTahunAkademik;
use App\Filament\Resources\TahunAkademiks\Schemas\TahunAkademikForm;
use App\Filament\Resources\TahunAkademiks\Schemas\TahunAkademikInfolist;
use App\Filament\Resources\TahunAkademiks\Tables\TahunAkademiksTable;
use App\Models\TahunAkademik;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TahunAkademikResource extends Resource
{
    protected static ?string $model = TahunAkademik::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'tahun';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Tahun Akademik';
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';
    public static function form(Schema $schema): Schema
    {
        return TahunAkademikForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TahunAkademikInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TahunAkademiksTable::configure($table);
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
            'index' => ListTahunAkademiks::route('/'),
            'create' => CreateTahunAkademik::route('/create'),
            'view' => ViewTahunAkademik::route('/{record}'),
            'edit' => EditTahunAkademik::route('/{record}/edit'),
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

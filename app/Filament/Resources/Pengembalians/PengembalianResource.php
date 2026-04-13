<?php

namespace App\Filament\Resources\Pengembalians;

use App\Filament\Resources\Pengembalians\Pages\CreatePengembalian;
use App\Filament\Resources\Pengembalians\Pages\EditPengembalian;
use App\Filament\Resources\Pengembalians\Pages\ListPengembalians;
use App\Filament\Resources\Pengembalians\Schemas\PengembalianForm;
use App\Filament\Resources\Pengembalians\Tables\PengembaliansTable;
use App\Models\Pengembalian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
class PengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;
    
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $modelLabel = 'Pengembalian';
    protected static ?string $pluralModelLabel = 'Pengembalian';

    
public static function canViewAny(): bool
{
    // Pakai titik dua dua kali (static call)
    return Auth::check() && Auth::user()->role === 'admin';
}


    public static function form(Schema $schema): Schema
    {
        return PengembalianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengembaliansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengembalians::route('/'),
            'create' => CreatePengembalian::route('/create'),
            'edit' => EditPengembalian::route('/{record}/edit'),
        ];
    }
}
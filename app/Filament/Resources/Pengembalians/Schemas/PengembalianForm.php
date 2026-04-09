<?php

namespace App\Filament\Resources\Pengembalians\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PengembalianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('peminjaman_id')
                            ->label('Cari Kode Peminjaman')
                            ->options(
                                Peminjaman::where('status', 'dipinjam')
                                    ->get()
                                    ->pluck('kode_peminjaman', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                // AUTO-FILL: Jika kode dipilih, hitung denda otomatis
                                $pinjam = Peminjaman::find($state);
                                if ($pinjam) {
                                    $tglKembali = Carbon::now();
                                    $batas = Carbon::parse($pinjam->batas_pengembalian);
                                    
                                    if ($tglKembali->gt($batas)) {
                                        $selisih = $tglKembali->diffInDays($batas);
                                        $set('denda', $selisih * 2000); // Misal denda 2rb/hari
                                        $set('catatan', "Terlambat $selisih hari.");
                                    } else {
                                        $set('denda', 0);
                                        $set('catatan', 'Kembali tepat waktu.');
                                    }
                                }
                            }),

                        DatePicker::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->default(now())
                            ->required(),

                        TextInput::make('denda')
                            ->label('Denda (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Select::make('kondisi_buku')
                            ->label('Kondisi Buku')
                            ->options([
                                'baik' => 'Baik',
                                'rusak' => 'Rusak',
                                'hilang' => 'Hilang',
                            ])
                            ->required(),
                    ]),

                Textarea::make('catatan')
                    ->label('Catatan Tambahan')
                    ->columnSpanFull(),
            ]);
    }
}
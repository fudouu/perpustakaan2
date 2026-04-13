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
    const HARGA_TELAT = 2000;
    const HARGA_RUSAK = 20000;
    const HARGA_HILANG = 50000;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('peminjaman_id')
    ->label('Cari Kode Peminjaman')
    // Kita tambahkan parameter $operation di dalam closure
    ->relationship('peminjaman', 'kode_peminjaman', function ($query, $operation) {
        // 1. Jika sedang membuat pengembalian BARU (Create)
        if ($operation === 'create') {
            return $query->where('status', 'dipinjam');
        }
        
        // 2. Jika sedang EDIT, jangan di-filter 'dipinjam' saja 
        // supaya data yang sudah 'dikembalikan' tetap muncul dan nggak kosong
        return $query;
    })
    ->searchable()
    ->preload()
    ->required()
    ->live()
    ->afterStateUpdated(fn ($get, $set) => self::updateOtomatis($get, $set)),

                        // NAMA KOLOM DISESUAIKAN: tanggal_kembali_aktual
                        DatePicker::make('tanggal_kembali_aktual')
                            ->label('Tanggal Kembali')
                            ->default(now())
                            ->required()
                            ->live() 
                            ->afterStateUpdated(fn ($get, $set) => self::updateOtomatis($get, $set)),

                        // NAMA KOLOM DISESUAIKAN: denda_dibayar
                        TextInput::make('denda_dibayar')
                            ->label('Total Denda (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->readOnly()
                            ->dehydrated()
                            ->default(0),

                        Select::make('kondisi_buku')
                            ->label('Kondisi Buku')
                            ->options([
                                'baik' => 'Baik',
                                'rusak' => 'Rusak',
                                'hilang' => 'Hilang',
                            ])
                            ->default('baik')
                            ->required()
                            ->live() 
                            ->afterStateUpdated(fn ($get, $set) => self::updateOtomatis($get, $set)),
                    ]),

                Textarea::make('catatan')
                    ->label('Rincian Denda')
                    ->columnSpanFull()
                    ->rows(3)
                    ->readOnly(),
            ]);
    }

    public static function updateOtomatis($get, $set)
    {
        $idPinjam = $get('peminjaman_id');
        if (!$idPinjam) {
            $set('denda_dibayar', 0); // Pakai nama denda_dibayar
            $set('catatan', '');
            return;
        }

        $pinjam = Peminjaman::find($idPinjam);
        if (!$pinjam) return;

        $totalDenda = 0;
        $rincian = [];

        $batas = Carbon::parse($pinjam->batas_pengembalian)->startOfDay();
        // Ambil dari tanggal_kembali_aktual
        $tglKembali = Carbon::parse($get('tanggal_kembali_aktual') ?? now())->startOfDay();

        if ($tglKembali->gt($batas)) {
            $hariTelat = $tglKembali->diffInDays($batas); 
            $hariTelatFix = ($hariTelat <= 0) ? 1 : $hariTelat;

            $dendaTelat = $hariTelatFix * self::HARGA_TELAT;
            $totalDenda += $dendaTelat;
            $rincian[] = "Terlambat $hariTelatFix Hari (Rp " . number_format($dendaTelat, 0, ',', '.') . ")";
        }

        $kondisi = $get('kondisi_buku');
        if ($kondisi === 'rusak') {
            $totalDenda += self::HARGA_RUSAK;
            $rincian[] = "Buku Rusak (Rp " . number_format(self::HARGA_RUSAK, 0, ',', '.') . ")";
        } elseif ($kondisi === 'hilang') {
            $totalDenda += self::HARGA_HILANG;
            $rincian[] = "Buku Hilang (Rp " . number_format(self::HARGA_HILANG, 0, ',', '.') . ")";
        }

        // SET KE KOLOM denda_dibayar
        $set('denda_dibayar', $totalDenda); 
        
        if (empty($rincian)) {
            $set('catatan', 'Kembali tepat waktu & kondisi baik.');
        } else {
            $set('catatan', implode(' + ', $rincian));
        }
    }
}
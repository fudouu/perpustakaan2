<?php

namespace App\Filament\Resources\Peminjamen\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PeminjamanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('kode_peminjaman')
                            ->label('Kode Peminjaman')
                            ->default('TRX-' . strtoupper(Str::random(5))) // Otomatis generate kode unik
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('siswa_id')
                            ->label('Siswa Peminjam')
                            ->relationship('siswa', 'nis') // Secara sistem nyari NIS
                            ->getOptionLabelFromRecordUsing(fn ($record) => optional($record->user)->nama_lengkap . ' (' . $record->nis . ')') // Tampilan di layarnya disulap jadi: Nama Siswa (NIS)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('buku_id')
                            ->label('Buku yang Dipinjam')
                            ->relationship('buku', 'judul')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('admin_id')
                            ->label('Admin Petugas')
                            ->relationship('admin', 'nama_lengkap')
                            ->default(fn () => Auth::id())
                            ->disabled() // Dikunci biar admin gak bisa malsuin nama admin lain
                            ->dehydrated() // Tetap disave ke database walau formnya dikunci
                            ->required(),

                        DatePicker::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required(),

                        DatePicker::make('batas_pengembalian')
                            ->label('Batas Pengembalian')
                            ->default(now()->addDays(7)) // Otomatis diset +7 hari dari sekarang
                            ->required(),

                        Select::make('status')
                            ->label('Status Peminjaman')
                            ->options([
                                'dipinjam' => 'Dipinjam',
                                'dikembalikan' => 'Dikembalikan',
                                'terlambat' => 'Terlambat', // Mempertahankan opsi keren Abang
                                'hilang' => 'Hilang',
                            ])
                            ->default('dipinjam')
                            ->required(),
                    ]),

                Grid::make(1)
                    ->schema([
                        Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
<?php

namespace App\Filament\Resources\Peminjamen\Schemas;

use App\Models\Peminjaman;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon; // Wajib ada untuk hitung tanggal

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
                            ->default(function () {
                                $terakhir = Peminjaman::latest('id')->first();
                                if (! $terakhir) {
                                    return 'PMJ-001';
                                }
                                $angkaTerakhir = intval(substr($terakhir->kode_peminjaman, 4));
                                return 'PMJ-' . str_pad($angkaTerakhir + 1, 3, '0', STR_PAD_LEFT);
                            })
                            ->readOnly()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('siswa_id')
                            ->label('Siswa Peminjam')
                            ->relationship('siswa', 'nis') 
                            ->getOptionLabelFromRecordUsing(fn ($record) => optional($record->user)->nama_lengkap . ' (' . $record->nis . ')') 
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('buku_id')
                         ->label('Buku yang Dipinjam')
                         ->relationship('buku', 'judul', function ($query) {
                         // 🔥 VALIDASI: Hanya tampilkan buku yang stoknya minimal 1
                        return $query->where('stok', '>', 0);
                         })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Hanya menampilkan buku yang tersedia di rak (stok > 0)'),

                        Select::make('admin_id')
                            ->label('Admin Petugas')
                            ->relationship('admin', 'nama_lengkap')
                            ->default(fn () => Auth::id())
                            ->disabled() 
                            ->dehydrated() 
                            ->required(),

                        // REVISI BAGIAN TANGGAL PINJAM
                        DatePicker::make('tanggal_pinjam')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required()
                            ->live() // Aktifkan live agar perubahan langsung diproses
                            ->afterStateUpdated(function ($state, $set) {
                                // Begitu tanggal pinjam diubah, otomatis set batas kembali +7 hari
                                if ($state) {
                                    $date = Carbon::parse($state)->addDays(7)->format('Y-m-d');
                                    $set('batas_pengembalian', $date);
                                }
                            }),

                        // REVISI BAGIAN BATAS PENGEMBALIAN
                        DatePicker::make('batas_pengembalian')
                            ->label('Batas Pengembalian')
                            ->default(now()->addDays(7)) 
                            ->required()
                            ->readOnly() // Dikunci total agar tidak bisa diubah manual
                            ->dehydrated() // Tetap dikirim ke database walau readOnly
                            ->helperText('Otomatis ditentukan 7 hari dari tanggal pinjam'),

                        Select::make('status')
                            ->label('Status Peminjaman')
                            ->options([
                                'dipinjam' => 'Dipinjam',
                                'dikembalikan' => 'Dikembalikan',
                                'terlambat' => 'Terlambat',
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
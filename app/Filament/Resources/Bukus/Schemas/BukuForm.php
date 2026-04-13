<?php

namespace App\Filament\Resources\Bukus\Schemas;

use App\Models\Buku; // Pastikan import Model Buku
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;

class BukuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Bungkus pertama: Dibagi jadi 2 kolom (kiri-kanan) - TETAP SESUAI KODE ABANG
                Grid::make(2)
                    ->schema([
                        TextInput::make('kode_buku')
                            ->label('Kode Buku')
                            ->default(function () {
                                // Ambil buku terakhir berdasarkan ID
                                $bukuTerakhir = Buku::latest('id')->first();
                                
                                // Jika belum ada buku, mulai dari B001
                                if (! $bukuTerakhir) {
                                    return 'B001';
                                }
                                
                                // Ambil angka setelah huruf 'B', tambah 1, lalu pad dengan nol
                                $angkaTerakhir = intval(substr($bukuTerakhir->kode_buku, 1));
                                return 'B' . str_pad($angkaTerakhir + 1, 3, '0', STR_PAD_LEFT);
                            })
                            ->readOnly() // Dikunci agar tidak bisa diubah manual
                            ->dehydrated() // Agar tetap tersimpan meski readOnly
                            ->unique(ignoreRecord: true)
                            ->required(),

                        TextInput::make('judul')
                            ->label('Judul Buku')
                            ->required(),

                        TextInput::make('penulis')
                            ->label('Penulis')
                            ->required(),

                        TextInput::make('penerbit')
                            ->label('Penerbit')
                            ->required(),

                        TextInput::make('tahun_terbit')
                            ->label('Tahun Terbit')
                            ->numeric() // Sesuai tipe data year
                            ->required(),

                        Select::make('kategori_id')
                            ->label('Kategori Buku')
                            ->relationship('kategori', 'nama_kategori') 
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('isbn')
                            ->label('ISBN'),

                        TextInput::make('jumlah_halaman')
                            ->label('Jml. Halaman')
                            ->numeric(),

                        TextInput::make('stok')
                            ->label('Stok')
                            ->required()
                            ->numeric()
                            ->default(0),

                        TextInput::make('lokasi_rak')
                            ->label('Lokasi Rak'),
                    ]),

                // Bungkus kedua: Dibiarkan 1 kolom penuh di bagian bawah - TETAP SESUAI KODE ABANG
                Grid::make(1)
                    ->schema([
                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->columnSpanFull(),

                        FileUpload::make('cover_image')
                            ->label('Cover Buku')
                            ->image(),
                    ]),
            ]);
    }
}
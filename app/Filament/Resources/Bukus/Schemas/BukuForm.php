<?php

namespace App\Filament\Resources\Bukus\Schemas;

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
                // Bungkus pertama: Dibagi jadi 2 kolom (kiri-kanan)
                Grid::make(2)
                    ->schema([
                        TextInput::make('kode_buku')
                            ->required(),
                        TextInput::make('judul')
                            ->required(),
                        TextInput::make('penulis')
                            ->required(),
                        TextInput::make('penerbit')
                            ->required(),
                        TextInput::make('tahun_terbit')
                            ->required(),
                        Select::make('kategori_id')
                            ->relationship('kategori', 'nama_kategori') 
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Kategori Buku'),
                        TextInput::make('isbn'),
                        TextInput::make('jumlah_halaman')
                            ->numeric(),
                        TextInput::make('stok')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('lokasi_rak'),
                    ]),

                // Bungkus kedua: Dibiarkan 1 kolom penuh di bagian bawah
                Grid::make(1)
                    ->schema([
                        Textarea::make('deskripsi')
                            ->columnSpanFull(),
                        FileUpload::make('cover_image')
                            ->image(),
                    ]),
            ]);
    }
}
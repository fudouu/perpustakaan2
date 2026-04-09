<?php

namespace App\Filament\Resources\Bukus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BukusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_buku')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('judul')
                    ->label('Judul Buku')
                    ->searchable()
                    ->weight('bold'),
                    
                // Mengambil nama kategori dari tabel relasi
                TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('penulis')
                    ->searchable(),
                    
                TextColumn::make('penerbit')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyi default biar rapi
                    
                TextColumn::make('tahun_terbit')
                    ->sortable(),
                    
                TextColumn::make('isbn')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('jumlah_halaman')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                // Stok dengan Badge Warna Otomatis
                TextColumn::make('stok')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state == 0 => 'danger',   // Merah kalau habis
                        $state <= 3 => 'warning',  // Kuning kalau mau habis
                        default => 'success',      // Hijau kalau aman
                    }),
                    
                TextColumn::make('lokasi_rak')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->circular(),
                    
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(), // Mengembalikan tombol Edit milik Abang
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(), // Mengembalikan tombol Delete milik Abang
                ]),
            ]);
    }
}
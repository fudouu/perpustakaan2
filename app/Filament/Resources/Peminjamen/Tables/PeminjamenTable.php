<?php

namespace App\Filament\Resources\Peminjamen\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class PeminjamenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_peminjaman')
                    ->label('Kode TRX')
                    ->searchable()
                    ->weight('bold'),

                // Manggil relasi ke tabel Siswa
                TextColumn::make('siswa.user.nama_lengkap')
                    ->label('Nama Peminjam')
                    ->searchable()
                    ->sortable(),

                // Manggil relasi ke tabel Buku
                TextColumn::make('buku.judul')
                    ->label('Buku')
                    ->searchable()
                    ->sortable()
                    ->limit(30), // Kalau judul panjang, otomatis dipotong pakai "..."

                TextColumn::make('tanggal_pinjam')
                    ->label('Tgl Pinjam')
                    ->date('d M Y') // Format tanggal ala Indonesia (misal: 09 Apr 2026)
                    ->sortable(),

                TextColumn::make('batas_pengembalian')
                    ->label('Batas Kembali')
                    ->date('d M Y')
                    ->sortable(),

                // MAGIC WIDGET: Status warna-warni menyesuaikan opsi Abang
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dipinjam'     => 'warning',  // Kuning (Masih jalan)
                        'dikembalikan' => 'success',  // Hijau (Aman)
                        'terlambat'    => 'danger',   // Merah (Bahaya)
                        'hilang'       => 'danger',   // Merah (Bahaya)
                        default        => 'gray',
                    }),

                // Disembunyikan secara default biar tabel nggak kepanjangan
                TextColumn::make('admin.nama_lengkap')
                    ->label('Petugas Admin')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('catatan')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tempat nambah filter nanti (misal filter khusus buku telat)
            ])
            ->recordActions([
                EditAction::make(), // Tombol Edit
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(), // Tombol Hapus Massal
                ]),
            ]);
    }
}
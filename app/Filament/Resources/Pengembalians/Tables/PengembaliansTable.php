<?php

namespace App\Filament\Resources\Pengembalians\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PengembaliansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('peminjaman.kode_peminjaman')
                    ->label('Kode Pinjam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('peminjaman.siswa.user.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable(),

                TextColumn::make('peminjaman.buku.judul')
                    ->label('Buku')
                    ->limit(20),

                TextColumn::make('tanggal_kembali')
                    ->label('Tgl Kembali')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('denda')
                    ->label('Denda')
                    ->money('IDR')
                    ->color('danger'),

                TextColumn::make('kondisi_buku')
                    ->label('Kondisi')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
<?php

namespace App\Filament\Widgets;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Siswa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Mengatur urutan widget agar muncul paling atas
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Judul Buku', Buku::count())
                ->description('Semua koleksi perpustakaan')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Grafik bohong-bohongan biar keren 📈

            Stat::make('Total Siswa', Siswa::count())
                ->description('Siswa terdaftar anggota')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([1, 4, 2, 8, 5, 9, 12]),

            Stat::make('Buku Sedang Dipinjam', Peminjaman::where('status', 'dipinjam')->count())
                ->description('Buku yang belum kembali')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([10, 8, 12, 5, 14, 10, 16]),
        ];
    }
}
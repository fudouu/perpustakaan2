<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    protected $fillable = [
        'peminjaman_id',
        'tanggal_kembali_aktual', 
        'denda_dibayar',          
        'kondisi_buku',
        'catatan',
        'admin_id',
    ];

    /**
     * Relasi ke tabel Peminjaman
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /**
     * Relasi ke tabel User (Petugas/Admin)
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Logika Otomatisasi Stok & Status
     */
    protected static function booted()
    {
        static::created(function ($pengembalian) {
            // Ambil data peminjaman yang terkait
            $peminjaman = $pengembalian->peminjaman;
            
            if ($peminjaman) {
                // 1. Update status peminjaman jadi 'dikembalikan'
                $peminjaman->update([
                    'status' => 'dikembalikan'
                ]);

                // 2. Tambah balik stok bukunya
                if ($peminjaman->buku) {
                    $peminjaman->buku->increment('stok');
                }
            }
        });

        // Tambahan: Kalau data pengembalian dihapus, stok dikurangi lagi 
        // (Biar data tetap sinkron kalau ada salah input)
        static::deleted(function ($pengembalian) {
            $peminjaman = $pengembalian->peminjaman;
            if ($peminjaman && $peminjaman->buku) {
                $peminjaman->buku->decrement('stok');
                $peminjaman->update(['status' => 'dipinjam']);
            }
        });
    }
}
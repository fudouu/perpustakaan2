<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $fillable = [
    'peminjaman_id',
    'tanggal_kembali',
    'denda',
    'kondisi_buku',
    'catatan',
];
    //
    // 1. Kenalkan dulu relasinya ke tabel Peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    // 2. Buat logikanya
    protected static function booted()
    {
        // Fungsi ini otomatis jalan SETELAH data Pengembalian baru berhasil disimpan
        static::created(function ($pengembalian) {
            
            $peminjaman = $pengembalian->peminjaman; // Cari data peminjaman aslinya
            
            if ($peminjaman) {
                // A. Ubah status peminjaman jadi "dikembalikan"
                $peminjaman->update([
                    'status' => 'dikembalikan'
                ]);

                // B. Kembalikan (tambah) stok bukunya 1
                $buku = $peminjaman->buku;
                if ($buku) {
                    $buku->increment('stok');
                }
            }
        });
    }
}

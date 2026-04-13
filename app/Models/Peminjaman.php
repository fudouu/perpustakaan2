<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model
{
    protected $fillable = [
        'kode_peminjaman',
        'siswa_id',
        'buku_id',
        'admin_id',
        'tanggal_pinjam',
        'batas_pengembalian',
        'status',
        'catatan',
    ];

    // Relasi ke Siswa
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke Buku
    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    // Relasi ke Admin (User)
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Booted function untuk menghandle logika otomatis
     */
    protected static function booted()
    {
        // OTOMATIS KURANGI STOK saat Peminjaman dibuat
        static::created(function ($peminjaman) {
            if ($peminjaman->buku) {
                $peminjaman->buku->decrement('stok');
            }
        });

        // OPTIONAL: Jika data peminjaman dihapus (cancel), balikin stoknya
        static::deleted(function ($peminjaman) {
            if ($peminjaman->buku) {
                $peminjaman->buku->increment('stok');
            }
        });
    }
}
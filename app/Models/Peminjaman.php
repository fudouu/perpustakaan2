<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    //
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    protected static function booted()
    {
        // Fungsi ini otomatis jalan SETELAH data Peminjaman baru berhasil disimpan
        static::created(function ($peminjaman) {
            $buku = $peminjaman->buku; // Ambil data buku yang dipinjam
            
            if ($buku) {
                $buku->decrement('stok'); // Kurangi stok buku 1
            }
        });
    }
}

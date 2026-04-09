<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    //
    protected $fillable = [
        'kode_buku',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'kategori_id',
        'isbn',
        'jumlah_halaman',
        'deskripsi',
        'stok',
        'lokasi_rak',
        'cover_image',
    ];
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Tambahkan 'user_id' ke dalam list ini Bang!
    protected $fillable = [
        'user_id', 
        'nis',
        'kelas',
        'jurusan',
        'tanggal_lahir',
        'status',
    ];

    // Pastikan relasi ini juga ada biar manggil namanya lancar
    public function user() {
    return $this->belongsTo(User::class, 'user_id');
}
}
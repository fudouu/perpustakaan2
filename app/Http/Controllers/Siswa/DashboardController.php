<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman; // Wajib dipanggil biar bisa nyatet riwayat
use App\Models\Siswa;      // Wajib dipanggil buat ngecek data siswa
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Fungsi 1: Menampilkan halaman depan (Katalog Buku)
    public function index()
    {
        // 1. Cek: Kalau belum login sama sekali, tendang ke login Filament
        if (!Auth::check()) {
            return redirect('/admin/login');
        }

        // 2. Cek Role: Kita cari apakah User ini ada di tabel Siswa
        $siswa = Siswa::where('user_id', Auth::id())->first();

        if (!$siswa) {
            // Kalau login tapi bukan siswa (berarti Admin), tendang ke Dashboard Admin
            return redirect('/admin');
        }

        // 3. Kalau beneran Siswa, baru izinkan lihat katalog buku
        $bukus = Buku::latest()->get();
        return view('siswa.dashboard', compact('bukus'));
    }

    // Fungsi 2: Menangani proses saat tombol "Pinjam" diklik
    public function pinjam(Request $request, Buku $buku)
    {
        // 1. Cek apakah stok masih ada
        if ($buku->stok < 1) {
            return back()->with('error', 'Maaf, stok buku sudah habis!');
        }

        // 2. Cari data Siswa berdasarkan user yang sedang login
        $siswa = Siswa::where('user_id', Auth::id())->first();
        
        // Safety net: Jaga-jaga kalau ada admin maksa pinjam lewat link
        if (!$siswa) {
            return back()->with('error', 'Hanya akun Siswa yang boleh meminjam buku!');
        }

        // 3. Catat transaksi ke tabel Peminjaman
        Peminjaman::create([
            'kode_peminjaman' => 'PJ-' . time(), 
            'siswa_id' => $siswa->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now(),
            'batas_pengembalian' => now()->addDays(7), 
            'status' => 'dipinjam',
        ]);

        // 4. Kurangi stok buku 1 buah
        $buku->decrement('stok');

        // 5. Kembalikan ke halaman katalog dengan notifikasi sukses
        return back()->with('success', 'Buku berhasil dipinjam!');
    }
}
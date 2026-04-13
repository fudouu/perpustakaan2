<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom di tabel Peminjaman
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->date('tanggal_kembali')->nullable()->after('batas_pengembalian');
            $table->decimal('denda', 10, 2)->nullable()->after('status');
        });

        // 2. Revisi kolom di tabel Pengembalian (KITA PISAH JADI 2 BLOK)
        
        // Blok A: Ganti nama dulu
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->renameColumn('tanggal_kembali', 'tanggal_kembali_aktual');
            $table->renameColumn('denda', 'denda_dibayar');
        });

        // Blok B: Baru tambah kolom baru (patokannya pakai nama yang baru)
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('peminjaman_id')->constrained('users')->cascadeOnDelete();
            $table->integer('keterlambatan')->default(0)->after('tanggal_kembali_aktual'); // <-- Aman!
        });
    }

    public function down(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->dropColumn(['tanggal_kembali', 'denda']);
        });

        Schema::table('pengembalians', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['admin_id', 'keterlambatan']);
        });

        Schema::table('pengembalians', function (Blueprint $table) {
            $table->renameColumn('tanggal_kembali_aktual', 'tanggal_kembali');
            $table->renameColumn('denda_dibayar', 'denda');
        });
    }
};
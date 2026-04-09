<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('pengembalians', function (Blueprint $table) {
        // Kita buat kolomnya dulu
        $table->unsignedBigInteger('peminjaman_id')->after('id');
        
        // Kita kunci relasinya secara manual ke tabel 'peminjamen'
        $table->foreign('peminjaman_id')
              ->references('id')
              ->on('peminjamen') // Sesuaikan dengan nama tabel Abang
              ->onDelete('cascade');

        // Tambah kolom pendukung lainnya
        $table->date('tanggal_kembali')->after('peminjaman_id');
        $table->integer('denda')->default(0)->after('tanggal_kembali');
        $table->enum('kondisi_buku', ['baik', 'rusak', 'hilang'])->after('denda');
        $table->text('catatan')->nullable()->after('kondisi_buku');
    });
}
    public function down(): void
    {
        Schema::table('pengembalians', function (Blueprint $table) {
            $table->dropForeign(['peminjaman_id']);
            $table->dropColumn(['peminjaman_id', 'tanggal_kembali', 'denda', 'kondisi_buku', 'catatan']);
        });
    }
};
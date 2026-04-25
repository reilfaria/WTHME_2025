<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('peserta','panitia','admin','bendahara') NOT NULL DEFAULT 'peserta'");
        }
        // SQLite (development): enum tidak di-enforce ketat.
        // Jika muncul error, jalankan: php artisan migrate:fresh --seed
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('peserta','panitia','admin') NOT NULL DEFAULT 'peserta'");
        }
    }
};

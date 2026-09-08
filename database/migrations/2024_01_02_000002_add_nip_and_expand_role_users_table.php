<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nip')) {
                $table->string('nip')->nullable()->unique()->after('nim');
            }
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM('mahasiswa','petugas','admin','dosen','pengunjung') NOT NULL DEFAULT 'mahasiswa'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nip')) {
                $table->dropColumn('nip');
            }
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM('mahasiswa','petugas','admin') NOT NULL DEFAULT 'mahasiswa'");
    }
};

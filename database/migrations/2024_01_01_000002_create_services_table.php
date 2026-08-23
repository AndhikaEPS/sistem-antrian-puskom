<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_code', 5)->unique()->comment('Kode prefix nomor antrian, misal AK, JN, LB, PK, IT');
            $table->string('service_name');
            $table->text('description')->nullable();
            $table->unsignedInteger('estimated_duration')->default(5)->comment('Estimasi durasi layanan dalam menit, dipakai sebagai default sebelum ada data historis');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

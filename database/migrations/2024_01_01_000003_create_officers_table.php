<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('counter_number')->comment('Nomor loket petugas bertugas');
            $table->enum('status', ['available', 'busy', 'offline'])->default('offline');
            $table->foreignId('current_service_id')->nullable()->constrained('services')->nullOnDelete()
                ->comment('Jenis layanan yang sedang ditangani petugas ini pada shift berjalan, null = melayani semua');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officers');
    }
};

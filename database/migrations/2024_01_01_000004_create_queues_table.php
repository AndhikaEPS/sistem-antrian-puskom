<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number', 15)->comment('Contoh: AK-001, JN-014');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->foreignId('officer_id')->nullable()->constrained('officers')->nullOnDelete();
            $table->date('queue_date');
            $table->time('queue_time');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['WAITING', 'CALLED', 'SERVING', 'COMPLETED', 'SKIPPED', 'CANCELLED'])
                ->default('WAITING');
            $table->unsignedTinyInteger('counter_number')->nullable();
            $table->unsignedTinyInteger('rating')->nullable()->comment('Rating kepuasan 1-5, diisi mahasiswa setelah selesai dilayani');
            $table->text('feedback')->nullable();
            $table->timestamps();

            // Satu nomor antrian unik per hari per layanan
            $table->unique(['service_id', 'queue_date', 'queue_number']);
            $table->index(['queue_date', 'status']);
            $table->index(['service_id', 'queue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};

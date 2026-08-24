<?php

namespace Database\Seeders;

use App\Models\Officer;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Pengaturan default ----------
        Setting::set('queue_number_padding', 3);
        Setting::set('office_start', '08:00');
        Setting::set('office_end', '16:00');
        Setting::set('default_service_minutes', 5);

        // ---------- Jenis Layanan ----------
        $services = [
            ['service_code' => 'AK', 'service_name' => 'Bantuan Akun & Sistem Informasi', 'description' => 'Bantuan akun mahasiswa, kendala login, reset akun.', 'estimated_duration' => 5, 'sort_order' => 1],
            ['service_code' => 'JN', 'service_name' => 'Jaringan & Internet', 'description' => 'Kendala koneksi internet, Wi-Fi, jaringan kampus.', 'estimated_duration' => 7, 'sort_order' => 2],
            ['service_code' => 'LB', 'service_name' => 'Laboratorium Komputer', 'description' => 'Peminjaman lab, pelaporan kerusakan komputer.', 'estimated_duration' => 6, 'sort_order' => 3],
            ['service_code' => 'PK', 'service_name' => 'Perangkat Komputer', 'description' => 'Komputer/printer bermasalah, instalasi software.', 'estimated_duration' => 8, 'sort_order' => 4],
            ['service_code' => 'IT', 'service_name' => 'Informasi & Konsultasi IT', 'description' => 'Konsultasi layanan IT & teknologi.', 'estimated_duration' => 4, 'sort_order' => 5],
        ];

        foreach ($services as $s) {
            Service::updateOrCreate(['service_code' => $s['service_code']], $s + ['status' => 'active']);
        }

        // ---------- Admin ----------
        $admin = User::updateOrCreate(
            ['email' => 'admin@puskom.unima.ac.id'],
            ['name' => 'Admin PUSKOM', 'password' => 'password', 'role' => 'admin']
        );

        // ---------- Petugas (3 loket) ----------
        $petugasData = [
            ['name' => 'Petugas 1', 'email' => 'petugas1@puskom.unima.ac.id', 'counter' => 1],
            ['name' => 'Petugas 2', 'email' => 'petugas2@puskom.unima.ac.id', 'counter' => 2],
            ['name' => 'Petugas 3', 'email' => 'petugas3@puskom.unima.ac.id', 'counter' => 3],
        ];

        foreach ($petugasData as $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                ['name' => $p['name'], 'password' => 'password', 'role' => 'petugas']
            );

            Officer::updateOrCreate(
                ['user_id' => $user->id],
                ['counter_number' => $p['counter'], 'status' => 'offline']
            );
        }

        // ---------- Mahasiswa contoh ----------
        $mahasiswaData = [
            ['name' => 'Deltriano Rombot', 'nim' => '20230101', 'email' => 'mahasiswa1@unima.ac.id'],
            ['name' => 'Grace Sondakh', 'nim' => '20230102', 'email' => 'mahasiswa2@unima.ac.id'],
            ['name' => 'Farel Mokodompit', 'nim' => '20230103', 'email' => 'mahasiswa3@unima.ac.id'],
        ];

        foreach ($mahasiswaData as $m) {
            User::updateOrCreate(
                ['email' => $m['email']],
                ['name' => $m['name'], 'nim' => $m['nim'], 'password' => 'password', 'role' => 'mahasiswa']
            );
        }

        $this->command->info('Seeder selesai. Akun contoh (password semua: "password"):');
        $this->command->info('Admin    : admin@puskom.unima.ac.id');
        $this->command->info('Petugas  : petugas1@puskom.unima.ac.id / petugas2@... / petugas3@...');
        $this->command->info('Mahasiswa: mahasiswa1@unima.ac.id / mahasiswa2@... / mahasiswa3@...');
    }
}

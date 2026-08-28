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
        // Sistem ini hanya menggunakan SATU jenis layanan yang menangani
        // seluruh kebutuhan administrasi mahasiswa.
        $services = [
            ['service_code' => 'DA', 'service_name' => 'Divisi Administrasi', 'description' => 'Pencicilan UKT, permasalahan KRS (Kartu Rencana Studi), peminjaman alat, dan layanan administrasi lainnya.', 'estimated_duration' => 10, 'sort_order' => 1],
        ];

        foreach ($services as $s) {
            Service::updateOrCreate(['service_code' => $s['service_code']], $s + ['status' => 'active']);
        }

        // Nonaktifkan jenis layanan lama (jika sebelumnya pernah dipakai)
        // agar tidak lagi tampil ke mahasiswa, tanpa menghapus riwayat data.
        Service::whereIn('service_code', ['AK', 'JN', 'LB', 'PK', 'IT'])->update(['status' => 'inactive']);

        // ---------- Admin ----------
        $admin = User::updateOrCreate(
            ['email' => 'admin@puskom.unima.ac.id'],
            ['name' => 'Admin PUSKOM', 'password' => 'password', 'role' => 'admin']
        );

        // ---------- Petugas (3 loket) ----------
        $petugasData = [
            ['name' => 'Petugas Loket 1', 'email' => 'petugas1@puskom.unima.ac.id', 'counter' => 1],
            ['name' => 'Petugas Loket 2', 'email' => 'petugas2@puskom.unima.ac.id', 'counter' => 2],
            ['name' => 'Petugas Loket 3', 'email' => 'petugas3@puskom.unima.ac.id', 'counter' => 3],
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

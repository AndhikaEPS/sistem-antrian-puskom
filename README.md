# Sistem Antrian dan Layanan PUSKOM Universitas Negeri Manado

Sistem antrian digital berbasis web untuk Pusat Komputer (PUSKOM) Universitas Negeri
Manado. Dibangun dengan **Laravel 10**, **Blade + Tailwind CSS**, **MySQL**, dan
**Chart.js**, dengan tema visual *Dark Navy + Ocean Blue + Cyan Glow*.

---

## 1. Fitur Utama

- **3 Role Pengguna**: Mahasiswa, Petugas, Admin (dibatasi middleware `role` di server, bukan hanya disembunyikan di frontend).
- **Algoritma FIFO** untuk urutan pelayanan antrian.
- **Nomor antrian otomatis** per jenis layanan, atomic & aman dari race condition (`SELECT ... FOR UPDATE` + DB transaction).
- **Estimasi waktu tunggu** menggunakan pendekatan *Simple Moving Average* dari data historis durasi pelayanan.
- **Real-time (near real-time)** update posisi antrian via AJAX polling.
- **Halaman Display TV** untuk ruang tunggu, dengan pengumuman suara otomatis (Web Speech API browser).
- **Dashboard Admin** dengan grafik statistik (Chart.js): tren harian, per layanan, jam ramai, per petugas.
- **Laporan** dengan filter (tanggal, layanan, petugas, status) + **export PDF & Excel**.
- Rating/kepuasan pelayanan setelah antrian selesai.
- Keamanan: hashing password, CSRF protection, validasi input, otorisasi berbasis role di setiap request.

---

## 2. Kebutuhan Sistem (Local)

Pastikan sudah terpasang di komputer Anda:

- PHP >= 8.1 (dengan ekstensi: `mbstring`, `pdo_mysql`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `gd`)
- Composer
- MySQL/MariaDB
- (Opsional) Node.js — **tidak wajib**, karena Tailwind & Chart.js dimuat lewat CDN pada project ini agar instalasi tetap sederhana.

---

## 3. Struktur Folder

```
puskom-antrian/
├── app/
│   ├── Models/            # User, Service, Officer, Queue, QueueLog, Setting
│   ├── Http/
│   │   ├── Controllers/   # Auth, Mahasiswa, Petugas, Admin, Display
│   │   └── Middleware/    # RoleMiddleware + middleware bawaan Laravel
│   ├── Exports/           # Export laporan ke Excel
│   └── Providers/
├── database/
│   ├── migrations/        # users, services, officers, queues, queue_logs, settings
│   └── seeders/           # Data contoh (admin, petugas, mahasiswa, 5 layanan)
├── resources/views/
│   ├── landing/            # Landing page publik
│   ├── auth/                # Login & Register
│   ├── mahasiswa/           # Dashboard, layanan, nomor antrian, riwayat
│   ├── petugas/              # Dashboard pemanggilan antrian
│   ├── admin/                # Dashboard, users, services, officers, reports
│   └── display/              # Halaman TV ruang tunggu
├── routes/web.php          # Semua rute + proteksi role
└── config/, bootstrap/, public/   # Kerangka inti Laravel
```

---

## 4. Panduan Instalasi & Menjalankan

### Langkah 1 — Ekstrak & masuk folder project
```bash
cd puskom-antrian
```

### Langkah 2 — Install dependency PHP
```bash
composer install
```
> Perintah ini akan mengunduh Laravel Framework beserta package pendukung
> (`barryvdh/laravel-dompdf` untuk export PDF, `maatwebsite/excel` untuk export
> Excel) dari Packagist.

### Langkah 3 — Siapkan file environment
```bash
cp .env.example .env
php artisan key:generate
```

### Langkah 4 — Buat database MySQL
Buat database kosong, misalnya bernama `puskom_antrian`, lalu sesuaikan kredensial
di file `.env`:
```
DB_DATABASE=puskom_antrian
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 5 — Jalankan migration & seeder
```bash
php artisan migrate --seed
```
Perintah ini akan membuat seluruh tabel dan mengisi data contoh:
- 5 jenis layanan (AK, JN, LB, PK, IT)
- 1 akun admin, 3 akun petugas (loket 1–3), 3 akun mahasiswa contoh

### Langkah 6 — Buat symbolic link storage (jika nanti menambah upload file)
```bash
php artisan storage:link
```

### Langkah 7 — Jalankan server lokal
```bash
php artisan serve
```
Buka browser ke **http://localhost:8000**

---

## 5. Akun Demo (password semua: `password`)

| Role      | Email                          |
|-----------|---------------------------------|
| Admin     | admin@puskom.unima.ac.id       |
| Petugas   | petugas1@puskom.unima.ac.id (loket 1) |
| Petugas   | petugas2@puskom.unima.ac.id (loket 2) |
| Mahasiswa | mahasiswa1@unima.ac.id         |

Halaman Display TV dapat diakses tanpa login di: **`/display`**

---

## 6. Alur Pengujian Cepat

1. Login sebagai **mahasiswa** → Dashboard → pilih layanan → **Ambil Nomor Antrian**.
2. Buka tab baru, login sebagai **petugas1** → atur "Layanan Ditangani" bila perlu → klik **Panggil Berikutnya**.
3. Kembali ke tab mahasiswa: status antrian akan otomatis berubah menjadi `CALLED` dalam beberapa detik (AJAX polling).
4. Buka `/display` di tab ketiga untuk melihat papan panggilan otomatis (dengan suara, jika browser mendukung Web Speech API).
5. Petugas klik **Mulai Melayani** → **Selesaikan**.
6. Login sebagai **admin** → cek Dashboard (grafik), Manajemen Layanan/Petugas, dan Laporan (coba export PDF/Excel).

---

## 7. Algoritma & Pendekatan Teknis

| Kebutuhan | Pendekatan |
|---|---|
| Urutan pelayanan | **FIFO (First In First Out)** berdasarkan `queue_time` |
| Nomor antrian unik | Sequential numbering per layanan per hari, dibungkus DB transaction + row locking (anti race condition) |
| Estimasi waktu tunggu | **Simple Moving Average** dari durasi pelayanan historis (`Service::averageServiceDuration()`), fallback ke nilai default admin |
| Pembagian ke loket | Setiap petugas dapat difokuskan ke satu jenis layanan (`current_service_id`), memanggil FIFO khusus layanan tersebut |
| Update real-time | AJAX Polling setiap 4 detik (dapat ditingkatkan ke Laravel Broadcasting/WebSocket) |

Lihat komentar kode pada `app/Models/Queue.php` dan
`app/Http/Controllers/Petugas/CallController.php` untuk detail implementasi.

---

## 8. Pengembangan Lanjutan (Saran)

- Ganti AJAX polling dengan **Laravel Broadcasting (Pusher/Soketi) + Echo** untuk update yang benar-benar real-time tanpa delay polling.
- Tambahkan **QR Code** pengambilan antrian (`simplesoftwareio/simple-qrcode`).
- Tambahkan notifikasi WhatsApp/Telegram saat nomor hampir dipanggil.
- Tambahkan halaman **Pengaturan** admin untuk jam operasional & format nomor antrian secara visual (saat ini tersedia lewat tabel `settings`).

---

## 9. Lisensi

Project ini dibuat sebagai contoh tugas akhir/magang mahasiswa Teknik Informatika.
Bebas dipakai dan dikembangkan lebih lanjut untuk keperluan akademik PUSKOM UNIMA.

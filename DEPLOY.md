# 🚀 Panduan Deployment - School Library

Panduan lengkap untuk deploy aplikasi **School Library** ke server (VPS, Home Server, Ubuntu) menggunakan Docker Compose.

---

## 📋 Daftar Isi

1. [Prasyarat](#1-prasyarat)
2. [Persiapan Server](#2-persiapan-server)
3. [Instalasi & Konfigurasi](#3-instalasi--konfigurasi)
4. [Menjalankan Aplikasi](#4-menjalankan-aplikasi)
5. [Pindah Server (Migrasi)](#5-pindah-server-migrasi)
6. [Maintenance & Backup](#6-maintenance--backup)
7. [Troubleshooting](#7-troubleshooting)

---

## 1. Prasyarat

Pastikan server Anda (Ubuntu 20.04/22.04 recommended) sudah terinstal:

- **Docker**: v24.0+
- **Docker Compose**: v2.20+
- **Git**

### Cara Install di Ubuntu

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Tambahkan user ke grup docker (agar tidak perlu sudo)
sudo usermod -aG docker $USER

# Logout dan login kembali
exit
```

---

## 2. Persiapan Server

```bash
# Buat direktori project
mkdir -p ~/library-app
cd ~/library-app
```

---

## 3. Instalasi & Konfigurasi

### 3.1 Clone Repository / Upload File

Jika menggunakan Git:
```bash
git clone https://github.com/username/library_app.git .
```
Atau upload file project manual via SFTP/SCP.

### 3.2 Setup Environment

```bash
# Copy file environment docker
cp .env.docker .env.docker.local

# Edit konfigurasi
nano .env.docker.local
```

**Wajib Diubah di `.env.docker.local`:**
1.  `APP_URL`: Masukkan IP server atau domain (contoh: `http://192.168.1.100`)
2.  `DB_PASSWORD`: Password database (bebas, tapi ingat untuk nanti)
3.  `DB_ROOT_PASSWORD`: Password root database

### 3.3 Setup Folder Data

Kita akan menggunakan **bind mounts** agar data tersimpan di folder project, bukan di dalam sistem Docker. Ini memudahkan backup dan pindah server.

```bash
# Buat folder untuk data database & redis
mkdir -p docker-data/mysql
mkdir -p docker-data/redis

# Pastikan folder storage memiliki permission yang benar (User ID 1000)
sudo chown -R 1000:1000 storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 4. Menjalankan Aplikasi

### 4.1 Build & Start

```bash
# Build image dan jalankan container
# Gunakan file override produksi untuk membatasi penggunaan RAM (cocok untuk 2GB RAM)
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

### 4.2 Inisialisasi Pertama Kali

Tunggu sekitar 1-2 menit sampai MySQL siap, lalu jalankan:

```bash
# 1. Generate Application Key
docker compose exec app php artisan key:generate --show
# -> Salin key yang muncul, masukkan ke .env.docker.local di bagian APP_KEY, lalu restart:
# docker compose restart app queue

# 2. Jalankan Migrasi & Seeder (Data Awal)
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force

# 3. Buat User Admin
docker compose exec app php artisan tinker
```

Di dalam Tinker (shell interaktif), ketik:

```php
\App\Models\User::create([
    'name' => 'Administrator',
    'email' => 'admin@library.com',
    'password' => bcrypt('password123'),
    'email_verified_at' => now(),
]);
exit
```

### 4.3 Akses Aplikasi

Buka browser dan akses: `http://IP-SERVER-ANDA`

---

## 5. Pindah Server (Migrasi)

Karena kita menggunakan **bind mounts**, memindahkan aplikasi ke server lain sangat mudah.

### Langkah-langkah:

1.  **Di Server Lama:**
    ```bash
    # Matikan container
    cd ~/library-app
    docker compose down
    ```

2.  **Copy Folder Project:**
    Salin **seluruh folder** `library-app` ke server baru. Folder ini berisi:
    - Code aplikasi
    - File `.env.docker` (konfigurasi)
    - Folder `docker-data/` (isi database MySQL & Redis)
    - Folder `storage/` (file upload)

    Contoh menggunakan `scp`:
    ```bash
    scp -r ~/library-app user@server-baru:/home/user/
    ```

3.  **Di Server Baru:**
    ```bash
    cd ~/library-app

    # Pastikan permission benar
    sudo chown -R 1000:1000 storage bootstrap/cache docker-data

    # Jalankan Docker
    docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
    ```

    Semua data (buku, peminjam, user) akan otomatis ada sama persis seperti di server lama.

---

## 6. Maintenance & Backup

### Update Aplikasi (Kode Baru)
Jika ada perubahan kode (git pull):
```bash
git pull origin main
docker compose up -d --build  # Rebuild image
```

### Backup Manual
Cukup zip satu folder project:
```bash
tar -czvf backup-library-$(date +%F).tar.gz ~/library-app
```

### Cek Log
```bash
docker compose logs -f app    # Log aplikasi
docker compose logs -f mysql  # Log database
docker compose logs -f nginx  # Log web server
```

---

## 7. Troubleshooting

**Error: `Permission denied` pada log `storage/logs/...`**
- Pastikan folder storage dimiliki oleh user ID 1000.
- Fix: `sudo chown -R 1000:1000 storage`

**Error: Database Connection Refused**
- MySQL mungkin belum siap (tunggu 1 menit).
- Cek log mysql: `docker compose logs mysql`
- Pastikan password di `.env.docker` sama dengan saat pertama kali container dibuat.

**Halaman Blank / 500 Server Error**
- Cek apakah `APP_KEY` sudah diisi di `.env.docker`.
- Cek log laravel: `docker compose exec app cat storage/logs/laravel.log`

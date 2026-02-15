# 🚀 Panduan Deployment - School Library

Panduan lengkap untuk deploy aplikasi **School Library** ke server menggunakan Docker Compose.

---

## 📋 Daftar Isi

1. [Prasyarat](#1-prasyarat)
2. [Persiapan Server](#2-persiapan-server)
3. [Clone Repository](#3-clone-repository)
4. [Konfigurasi Environment](#4-konfigurasi-environment)
5. [Build & Jalankan](#5-build--jalankan)
6. [Inisialisasi Aplikasi](#6-inisialisasi-aplikasi)
7. [Setup Domain & SSL (Opsional)](#7-setup-domain--ssl-opsional)
8. [Maintenance & Operasi](#8-maintenance--operasi)
9. [Backup & Restore](#9-backup--restore)
10. [Troubleshooting](#10-troubleshooting)

---

## 1. Prasyarat

Pastikan server Anda sudah terinstal:

| Software | Versi Minimum | Cek Versi |
|----------|--------------|-----------|
| Docker | 24.0+ | `docker --version` |
| Docker Compose | v2.20+ | `docker compose version` |
| Git | 2.30+ | `git --version` |

### Install Docker di Ubuntu/Debian

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Tambahkan user ke grup docker (agar tidak perlu sudo)
sudo usermod -aG docker $USER

# Logout dan login kembali, lalu verifikasi
docker --version
docker compose version
```

---

## 2. Persiapan Server

### Buat Direktori Project

```bash
# Buat direktori untuk aplikasi
sudo mkdir -p /opt/library-app
sudo chown $USER:$USER /opt/library-app
cd /opt/library-app
```

### Buka Port yang Diperlukan

```bash
# Jika menggunakan UFW (Ubuntu):
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS (jika pakai SSL)
sudo ufw reload
```

---

## 3. Clone Repository

```bash
cd /opt/library-app

# Clone dari repository
git clone https://github.com/username/library_app.git .

# Atau jika sudah ada, pull terbaru
git pull origin main
```

---

## 4. Konfigurasi Environment

### 4.1 Buat File .env.docker

```bash
# Salin template environment
cp .env.docker .env.docker.local

# ATAU langsung edit .env.docker
nano .env.docker
```

### 4.2 Sesuaikan Konfigurasi

Buka `.env.docker` dan ubah bagian-bagian berikut:

```env
# ⚠️ WAJIB DIUBAH ===========================

# Ganti dengan URL/domain server Anda
APP_URL=http://your-domain.com

# Generate app key nanti setelah build (lihat langkah 6)
APP_KEY=

# Ganti password database (GUNAKAN PASSWORD KUAT!)
DB_PASSWORD=GantiDenganPasswordKuat123!
DB_ROOT_PASSWORD=GantiDenganRootPasswordKuat456!

# ============================================

# Opsional - sesuaikan port jika perlu
APP_PORT=80
DB_EXTERNAL_PORT=3306
```

> ⚠️ **PENTING**: Jangan gunakan password default di production! Gunakan password yang kuat dan unik.

---

## 5. Build & Jalankan

### 5.1 Build Images

```bash
cd /opt/library-app

# Build pertama kali (memakan waktu 3-10 menit)
docker compose build --no-cache
```

### 5.2 Jalankan Semua Services

```bash
# Mode development (melihat log langsung):
docker compose up

# Mode production (jalan di background):
docker compose up -d

# Atau dengan production overrides (direkomendasikan):
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### 5.3 Cek Status

```bash
# Lihat status semua containers
docker compose ps

# Output yang diharapkan:
# NAME              STATUS              PORTS
# library-app       Up (healthy)        9000/tcp
# library-nginx     Up                  0.0.0.0:80->80/tcp
# library-mysql     Up (healthy)        3306/tcp
# library-redis     Up (healthy)        6379/tcp
# library-queue     Up                  
```

---

## 6. Inisialisasi Aplikasi

### 6.1 Generate Application Key

```bash
# Generate APP_KEY
docker compose exec app php artisan key:generate --show

# Salin key yang dihasilkan (base64:xxxx...), lalu masukkan ke .env.docker
nano .env.docker
# Paste pada baris: APP_KEY=base64:xxxx...

# Restart app dengan key baru
docker compose restart app queue
```

### 6.2 Jalankan Database Seeder (Data Awal)

```bash
# Jalankan seeder jika tersedia
docker compose exec app php artisan db:seed --force
```

### 6.3 Buat User Admin Pertama

```bash
# Masuk ke container, buka Tinker
docker compose exec app php artisan tinker
```

Di dalam Tinker, buat user admin:

```php
\App\Models\User::create([
    'name' => 'Administrator',
    'email' => 'admin@library.com',
    'password' => bcrypt('password_anda'),
    'email_verified_at' => now(),
]);
```

Tekan `Ctrl+D` untuk keluar dari Tinker.

### 6.4 Verifikasi Instalasi

```bash
# Cek status migrasi
docker compose exec app php artisan migrate:status

# Cek koneksi database
docker compose exec app php artisan db:show

# Cek route list
docker compose exec app php artisan route:list --compact
```

### 6.5 Akses Aplikasi

Buka browser dan akses:
- **HTTP**: `http://your-server-ip` atau `http://your-domain.com`
- Login dengan akun admin yang baru dibuat.

---

## 7. Setup Domain & SSL (Opsional)

Jika ingin menggunakan domain dan HTTPS, gunakan Nginx reverse proxy di host:

### 7.1 Install Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
```

### 7.2 Buat Config Nginx di Host

```bash
sudo nano /etc/nginx/sites-available/library
```

```nginx
server {
    server_name your-domain.com;

    location / {
        proxy_pass http://127.0.0.1:80;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        client_max_body_size 64M;
    }
}
```

```bash
# Enable dan restart
sudo ln -s /etc/nginx/sites-available/library /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

# Generate SSL certificate
sudo certbot --nginx -d your-domain.com
```

### 7.3 Update Environment

```bash
# Ubah APP_URL di .env.docker
# APP_URL=https://your-domain.com

# Ubah APP_PORT agar tidak konflik dengan Nginx host
# APP_PORT=8080

# Restart
docker compose restart
```

---

## 8. Maintenance & Operasi

### 8.1 Melihat Log

```bash
# Log semua services
docker compose logs -f

# Log spesifik service
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f mysql
docker compose logs -f queue

# Log Laravel
docker compose exec app tail -f storage/logs/laravel.log
```

### 8.2 Update Aplikasi

```bash
cd /opt/library-app

# 1. Pull update terbaru
git pull origin main

# 2. Rebuild dan restart
docker compose build --no-cache
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# 3. Clear cache (jika perlu)
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### 8.3 Menjalankan Artisan Commands

```bash
# Format umum:
docker compose exec app php artisan <command>

# Contoh:
docker compose exec app php artisan migrate:status
docker compose exec app php artisan cache:clear
docker compose exec app php artisan queue:restart
```

### 8.4 Masuk ke Container

```bash
# Masuk ke container app (shell)
docker compose exec app sh

# Masuk ke MySQL
docker compose exec mysql mysql -u library_user -p school_library
```

### 8.5 Stop & Start Services

```bash
# Stop semua (data tetap aman di volumes)
docker compose down

# Stop dan HAPUS semua data (⚠️ HATI-HATI!)
docker compose down -v

# Start kembali
docker compose up -d

# Restart satu service
docker compose restart app
```

---

## 9. Backup & Restore

### 9.1 Backup Database

```bash
# Backup otomatis via aplikasi (download dari UI)
# Login → Pengaturan → Backup

# Backup manual via command line:
docker compose exec mysql mysqldump \
    -u library_user \
    -p'GantiDenganPasswordAnda' \
    school_library > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 9.2 Backup Storage (File Upload)

```bash
# Backup volume storage
docker run --rm \
    -v library_app_app_storage:/data \
    -v $(pwd)/backups:/backup \
    alpine tar czf /backup/storage_$(date +%Y%m%d_%H%M%S).tar.gz /data
```

### 9.3 Restore Database

```bash
# Restore dari file SQL
cat backup_file.sql | docker compose exec -T mysql \
    mysql -u library_user -p'PasswordAnda' school_library
```

### 9.4 Setup Backup Otomatis (Cron)

```bash
# Buat script backup
cat > /opt/library-app/backup.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/opt/library-app/backups"
mkdir -p $BACKUP_DIR
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
docker compose -f /opt/library-app/docker-compose.yml exec -T mysql \
    mysqldump -u library_user -p'PasswordAnda' school_library \
    > $BACKUP_DIR/db_$DATE.sql

# Hapus backup lebih dari 7 hari
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete

echo "Backup completed: $DATE"
EOF

chmod +x /opt/library-app/backup.sh

# Tambah ke crontab (backup setiap hari jam 2 pagi)
(crontab -l 2>/dev/null; echo "0 2 * * * /opt/library-app/backup.sh >> /opt/library-app/backups/cron.log 2>&1") | crontab -
```

---

## 10. Troubleshooting

### Container tidak mau start

```bash
# Cek log error
docker compose logs app
docker compose logs mysql

# Rebuild dari awal
docker compose down
docker compose build --no-cache
docker compose up -d
```

### MySQL connection refused

```bash
# Cek apakah MySQL sudah ready
docker compose ps mysql

# Cek healthcheck
docker inspect library-mysql | grep -A 10 "Health"

# Restart MySQL
docker compose restart mysql

# Tunggu 30 detik lalu restart app
sleep 30 && docker compose restart app
```

### Permission error pada storage

```bash
# Fix permissions dari dalam container
docker compose exec app chown -R www:www storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Halaman blank / 500 error

```bash
# Cek Laravel log
docker compose exec app cat storage/logs/laravel.log

# Cek apakah APP_KEY sudah diisi
docker compose exec app php artisan key:generate --show

# Clear semua cache
docker compose exec app php artisan optimize:clear
```

### Port sudah terpakai

```bash
# Ubah port di .env.docker
# APP_PORT=8080
# DB_EXTERNAL_PORT=33060

# Restart
docker compose down && docker compose up -d
```

### Reset total (mulai dari awal)

```bash
# ⚠️ PERINGATAN: Ini menghapus SEMUA data termasuk database!
docker compose down -v --rmi all
docker compose build --no-cache
docker compose up -d
```

---

## 📁 Struktur File Docker

```
library_app/
├── Dockerfile                    # Multi-stage build config
├── .dockerignore                 # File yang di-exclude dari build
├── docker-compose.yml            # Konfigurasi utama services
├── docker-compose.prod.yml       # Production overrides
├── .env.docker                   # Environment template untuk Docker
└── docker/
    ├── nginx/
    │   └── default.conf          # Konfigurasi Nginx
    ├── php/
    │   └── php.ini               # Konfigurasi PHP production
    └── entrypoint.sh             # Script startup container
```

---

## ⚡ Quick Start (TLDR)

```bash
# 1. Clone
git clone https://github.com/username/library_app.git /opt/library-app
cd /opt/library-app

# 2. Configure
nano .env.docker   # Ubah password & APP_URL

# 3. Build & Run
docker compose up -d --build

# 4. Generate Key
docker compose exec app php artisan key:generate --show
# Copy key ke .env.docker → restart: docker compose restart app queue

# 5. Seed (opsional)
docker compose exec app php artisan db:seed --force

# 6. Buat Admin
docker compose exec app php artisan tinker
# \App\Models\User::create(['name'=>'Admin','email'=>'admin@mail.com','password'=>bcrypt('pass123'),'email_verified_at'=>now()]);

# 7. Akses di browser → http://your-server-ip
```

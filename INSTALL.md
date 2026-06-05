# Panduan Instalasi ERP System

Panduan ini untuk menginstall ERP System di komputer Anda. Tidak memerlukan pengetahuan coding.

---

## Kebutuhan Sistem

| Komponen | Versi Minimum | Download |
|---|---|---|
| PHP | 8.2+ | https://windows.php.net/download |
| MySQL | 8.0+ | https://dev.mysql.com/downloads/mysql |
| Composer | Terbaru | https://getcomposer.org/download |
| Git | Terbaru | https://git-scm.com/download/win |

> **Catatan:** Git hanya dibutuhkan untuk download aplikasi utama. Plugin tidak memerlukan Git.

---

## Langkah 1 — Download Aplikasi

Buka **Command Prompt** atau **PowerShell**, lalu jalankan:

```bash
git clone https://github.com/febriandto/erp-app.git
cd erp-app
```

---

## Langkah 2 — Install Dependensi

```bash
composer install
```

Tunggu hingga selesai (membutuhkan koneksi internet).

---

## Langkah 3 — Konfigurasi

**3a.** Salin file konfigurasi:

```bash
copy .env.example .env
```

**3b.** Buat application key:

```bash
php artisan key:generate
```

**3c.** Buka file `.env` dengan Notepad, ubah bagian database sesuai komputer Anda:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_app        ← nama database yang akan dibuat
DB_USERNAME=root           ← username MySQL Anda
DB_PASSWORD=               ← password MySQL Anda
```

---

## Langkah 4 — Siapkan Database

**4a.** Buka MySQL dan buat database baru:

```sql
CREATE DATABASE erp_app;
```

**4b.** Jalankan migrasi:

```bash
php artisan migrate
```

---

## Langkah 5 — Jalankan Aplikasi

```bash
php artisan serve
```

Buka browser dan akses: **http://localhost:8000**

Aplikasi siap digunakan.

---

## Langkah 6 — Install Plugin

Plugin diinstall langsung dari dalam aplikasi — **tidak perlu Git atau akun GitHub.**

1. Buka **http://localhost:8000/admin/plugins**
2. Scroll ke bagian **Plugin Marketplace**
3. Klik tombol **Install** pada plugin yang diinginkan
4. Tunggu proses download selesai
5. Klik **Activate** untuk mengaktifkan plugin

![Plugin Manager](https://placehold.co/800x300?text=Plugin+Manager)

---

## Troubleshooting

### "php is not recognized..."
PHP belum ditambahkan ke PATH. Tambahkan folder PHP ke System Environment Variables, atau gunakan path lengkap:
```bash
C:\php\php.exe artisan serve
```

### "SQLSTATE: Connection refused"
MySQL belum berjalan. Buka **Services** di Windows dan start **MySQL**.

### Plugin gagal diinstall
- Pastikan koneksi internet aktif
- Pastikan folder `plugins/` dapat ditulis (write permission)

### Halaman error setelah activate plugin
```bash
php artisan cache:clear
php artisan config:clear
```

---

## Menjalankan Kembali Setelah Restart

Setiap kali komputer di-restart, jalankan perintah ini:

```bash
cd erp-app
php artisan serve
```

Lalu buka **http://localhost:8000**.

---

## Informasi

- Repository: https://github.com/febriandto/erp-app
- Plugin Registry: https://github.com/febriandto/erp-plugin-registry

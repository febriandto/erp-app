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

**4c.** Isi data awal (admin, role, dan modul inti):

```bash
php artisan db:seed
```

Perintah ini membuat:
- Akun **Administrator** (login pertama kali)
- Role `admin` dengan akses penuh
- Modul inti: **User Management** dan **Master Data**

---

## Langkah 5 — Jalankan Aplikasi

```bash
php artisan serve
```

Buka browser dan akses: **http://localhost:8000**

### Login pertama kali

| Field | Value |
|---|---|
| Email / Username | `admin@erp.local` atau `admin` |
| Password | `password` |

> **Penting:** Segera ganti password setelah login pertama kali melalui menu profil.

---

## Langkah 7 — Install Plugin

Plugin diinstall langsung dari dalam aplikasi — **tidak perlu Git atau akun GitHub.**

1. Buka menu **Plugins** di navbar kanan atas *(hanya tampil untuk Administrator)*
2. Scroll ke bagian **Plugin Marketplace**
3. Klik tombol **Install** pada plugin yang diinginkan
4. Tunggu proses download selesai
5. Klik **Actions → Activate** untuk mengaktifkan plugin

Plugin yang tersedia di marketplace:
- **Accounting** — invoice dan pembukuan
- **Inventory** — manajemen produk dan stok
- **Sales Order** — manajemen penjualan

---

## Menjalankan Kembali Setelah Restart

Setiap kali komputer di-restart, jalankan perintah ini:

```bash
cd erp-app
php artisan serve
```

Lalu buka **http://localhost:8000**.

---

## Update Aplikasi

Untuk mendapatkan versi terbaru aplikasi, jalankan **satu perintah** ini:

```bash
php artisan app:update
```

Perintah ini otomatis:
- Download update terbaru dari server
- Sync plugin yang ada
- Jalankan migrasi database baru (jika ada)
- Bersihkan cache

> **Catatan:** Tidak perlu menjalankan `git pull` secara manual.

---

## Troubleshooting

### "php is not recognized..."
PHP belum ditambahkan ke PATH. Gunakan path lengkap:
```bash
C:\php\php.exe artisan serve
```

### "SQLSTATE: Connection refused"
MySQL belum berjalan. Buka **Services** di Windows dan start **MySQL**.

### Menu "Plugins" tidak muncul di navbar
Menu Plugins hanya tampil untuk akun dengan role **Administrator**. Pastikan login menggunakan akun admin.

### Plugin gagal diinstall
- Pastikan koneksi internet aktif
- Pastikan folder `plugins/` dapat ditulis (write permission)
- Coba lagi — kadang timeout saat download dari GitHub

### Halaman error setelah activate plugin
```bash
php artisan cache:clear
php artisan config:clear
```

### Halaman error setelah update aplikasi
Jika muncul error setelah `app:update`, coba bersihkan cache manual:
```bash
php artisan view:clear
php artisan cache:clear
```

### Lupa password admin
```bash
php artisan tinker
> App\Models\User::where('email','admin@erp.local')->first()->update(['password' => bcrypt('password_baru')]);
```

---

## Informasi

- Repository: https://github.com/febriandto/erp-app
- Plugin Registry: https://github.com/febriandto/erp-plugin-registry

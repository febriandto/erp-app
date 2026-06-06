# CLAUDE.md — ERP App

Panduan ini membantu Claude Code memahami struktur, konvensi, dan cara kerja project ini.

---

## Stack

- **Framework**: Laravel 12 (PHP 8.5)
- **UI**: Tabler Admin (Bootstrap 5) + Tabler Icons (`ti ti-*`)
- **Database**: MySQL
- **Frontend Build**: Vite + NPM
- **PHP Binary**: `C:\sdk\php85\php.exe`
- **Composer**: `composer85` (terikat ke PHP 8.5)

---

## Arsitektur: Plugin System

Project ini menggunakan **arsitektur modular berbasis plugin** — mirip Odoo/WordPress. Setiap fitur ERP (Accounting, Inventory, HR, dll) adalah plugin independen yang bisa install/uninstall tanpa menyentuh core.

### Struktur Folder

```
erp-app/
├── app/
│   ├── Core/
│   │   ├── PluginManager.php      # Load, install, activate, deactivate plugin
│   │   └── MenuManager.php        # Kelola menu sidebar dinamis
│   ├── Http/Controllers/
│   │   └── PluginController.php   # UI Plugin Manager
│   ├── Models/
│   │   └── Plugin.php             # Model tabel plugins
│   └── Providers/
│       └── AppServiceProvider.php # Boot PluginManager + View Composer menu
├── plugins/                       # Semua plugin (Git Submodules)
│   ├── accounting/                # → github.com/febriandto/erp-plugin-accounting
│   └── inventory/                 # → github.com/febriandto/erp-plugin-inventory
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php          # Layout utama Tabler (sidebar dinamis)
│   ├── dashboard.blade.php
│   └── plugins/
│       └── index.blade.php        # Halaman Plugin Manager
├── config/
│   └── plugins.php                # URL registry marketplace
└── .gitmodules                    # Daftar submodule plugin
```

### Struktur Tiap Plugin

```
plugins/{slug}/
├── plugin.json          # Manifest: nama, versi, author, depends
├── Plugin.php           # Entry point (extends ServiceProvider)
├── Controllers/
├── Models/
├── migrations/
├── resources/views/
└── routes.php
```

---

## Cara Kerja Plugin System

### Boot Flow

```
Laravel boot
  → AppServiceProvider::boot()
  → PluginManager::loadActive()         # Query DB, ambil plugin is_active = true
  → require plugins/{slug}/Plugin.php
  → app()->register(Plugins\{slug}\Plugin::class)
  → Plugin::boot() → daftarkan routes, views, migrations, menu
  → View Composer dipanggil saat render layouts.app
  → MenuManager::all() → $menuItems ke sidebar
```

### Namespace Convention

```php
// Plugin entry point
namespace Plugins\accounting;

// Controller
namespace Plugins\accounting\Controllers;

// Model
namespace Plugins\accounting\Models;
```

### Membuat Plugin Baru

1. Buat folder `plugins/{slug}/`
2. Buat `plugin.json`:
```json
{
    "name": "Nama Plugin",
    "slug": "slug-plugin",
    "version": "1.0.0",
    "description": "Deskripsi singkat",
    "author": "febriandto",
    "depends": []
}
```
3. Buat `Plugin.php`:
```php
<?php

namespace Plugins\{slug};

use App\Core\MenuManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', '{slug}');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        Route::middleware(['web', 'auth'])->group(__DIR__ . '/routes.php');

        $this->app->make(MenuManager::class)->add([
            'title'  => 'Nama Menu',
            'url'    => route('{slug}.index'),
            'icon'   => 'ti ti-icon-name',
            'order'  => 50,
            'active' => '{slug}*',
        ]);
    }
}
```
4. Jalankan `composer85 dump-autoload`
5. Insert ke tabel `plugins` via tinker atau artisan command
6. Activate via UI Plugin Manager

---

## Artisan Commands

```bash
# Gunakan selalu PHP 8.5
C:\sdk\php85\php.exe artisan <command>

# Plugin management
C:\sdk\php85\php.exe artisan plugin:list
C:\sdk\php85\php.exe artisan plugin:install https://github.com/user/erp-plugin-hr
C:\sdk\php85\php.exe artisan plugin:activate hr
C:\sdk\php85\php.exe artisan plugin:deactivate hr

# Database
C:\sdk\php85\php.exe artisan migrate
C:\sdk\php85\php.exe artisan tinker
```

---

## Git Workflow

Project ini menggunakan **Git Submodules** untuk plugin.

```bash
# Clone project + semua plugin
git clone --recurse-submodules https://github.com/febriandto/erp-app

# Update semua submodule
git submodule update --remote --merge

# Develop plugin (contoh: accounting)
cd plugins/accounting
# ... edit file ...
git add . && git commit -m "pesan" && git push

# Update pointer di repo utama
cd ../..
git add plugins/accounting
git commit -m "update accounting plugin"
git push
```

### Script shortcut (Git Bash)

```bash
./push-plugin.sh accounting "pesan commit"
```

---

## Plugin Registry (Marketplace)

Registry disimpan di: `https://github.com/febriandto/erp-plugin-registry`

File `registry.json` berisi daftar semua plugin yang tersedia di marketplace. Update manual saat ada plugin baru atau perubahan versi.

Config URL registry: `config/plugins.php`

---

## View Convention

Semua view plugin menggunakan namespace blade:

```php
// Di controller
return view('accounting::invoices.index', compact('invoices'));

// Path file sebenarnya
plugins/accounting/resources/views/invoices/index.blade.php
```

Layout utama:
```blade
@extends('layouts.app')

@section('title', 'Judul Tab')
@section('page-title', 'Judul Halaman')

@section('page-actions')
{{-- Tombol di kanan header --}}
@endsection

@section('content')
{{-- Konten halaman --}}
@endsection
```

---

## Database

- Koneksi: MySQL via `.env`
- Tabel core: `users`, `sessions`, `cache`, `jobs`, `migrations`, `plugins`
- Tabel plugin: dikelola oleh migration masing-masing plugin
- Uninstall dengan Remove Data → `Schema::dropIfExists` pada tabel plugin

---

## UI Components (Tabler)

```html
<!-- Card -->
<div class="card">
    <div class="card-header"><h3 class="card-title">Judul</h3></div>
    <div class="card-body">...</div>
</div>

<!-- Table -->
<div class="table-responsive">
    <table class="table table-vcenter card-table">...</table>
</div>

<!-- Alert dari session -->
@if(session('success'))
<div class="alert alert-success alert-dismissible">...</div>
@endif

<!-- Icon (Tabler Icons) -->
<i class="ti ti-package"></i>
<i class="ti ti-file-invoice"></i>
<i class="ti ti-users"></i>
```

Referensi icon: https://tabler.io/icons
Referensi komponen: https://tabler.io/docs

---

## Micro Animation Standards

Stack animasi: **Alpine.js** (JS-driven) + **CSS utility classes** (visual transitions).
Definisi CSS ada di `resources/css/app.css`. Alpine di-init di `resources/js/app.js`.

### CSS Classes (gunakan konsisten)

| Class | Kapan dipakai |
|---|---|
| `.anim-fadein` | Card/section utama saat page load |
| `.anim-stagger` | Wrapper list/grid — tiap child masuk dengan delay 50ms bertahap |
| `.card-hover` | Card yang interaktif (marketplace item, selectable card) |

```html
<!-- Page load: card fade-in -->
<div class="card anim-fadein">...</div>

<!-- Grid dengan stagger (item masuk satu per satu) -->
<div class="row g-3 anim-stagger">
    <div class="col-md-4"><div class="card card-hover">...</div></div>
    <div class="col-md-4"><div class="card card-hover">...</div></div>
</div>

<!-- Table rows stagger -->
<tbody class="anim-stagger">
    @foreach($items as $item)
    <tr>...</tr>
    @endforeach
</tbody>

<!-- Delay manual jika ada dua section berurutan -->
<div class="card anim-fadein" style="animation-delay: 100ms">...</div>
```

### Alpine.js: Button Loading State

**Wajib** dipakai di semua tombol yang trigger form submit (activate, install, update, delete, dll).

```html
<!-- Pattern standar: x-data + @submit di FORM, bukan @click di button -->
<!-- PENTING: jangan pakai @click + :disabled di button submit —          -->
<!-- Alpine set disabled SEBELUM browser sempat submit, form tidak jalan  -->
<form action="..." method="POST" x-data="{ loading: false }" @submit="loading = true">
    @csrf
    <button type="submit">
        <span x-show="!loading">Label Tombol</span>
        <span x-show="loading" x-cloak>
            <span class="spinner-border spinner-border-sm me-1"></span>Loading...
        </span>
    </button>
</form>
```

### Aturan

1. **Jangan animasi elemen yang tidak interaktif** — teks biasa, label, badge status tidak perlu animasi
2. **`x-cloak` wajib** pada elemen yang di-`x-show` agar tidak flash sebelum Alpine init
3. **Jangan tambah CSS animasi baru** di view atau plugin — gunakan class yang sudah ada di `app.css`
4. **Durasi standar**: 0.25s untuk fade/enter, 0.2s untuk hover transitions — jangan lebih lambat
5. **Setelah `npm run build`** wajib dijalankan setiap ada perubahan di `resources/`

---

## Hal Penting yang Perlu Diingat

1. **Selalu gunakan** `C:\sdk\php85\php.exe` dan `composer85` — bukan `php` atau `composer` biasa (masih PHP 8.0)
2. **Routes plugin HARUS pakai** `Route::middleware(['web', 'auth'])->group(...)` bukan `loadRoutesFrom()` — supaya session dan `$errors` tersedia di view
3. **Menu sidebar dinamis** — jangan hardcode di `app.blade.php`, daftarkan via `MenuManager::add()` di `Plugin::boot()`
4. **Namespace plugin** menggunakan `Plugins\{slug}\...` bukan `App\Modules\...`
5. **Autoload** — setelah tambah plugin baru jalankan `composer85 dump-autoload`
6. **View Composer** digunakan untuk `$menuItems` (bukan `View::share`) karena timing issue saat boot
7. **Micro animation** — gunakan class `.anim-fadein`, `.anim-stagger`, `.card-hover` dan Alpine `x-data="{ loading: false }"` sesuai standar di section "Micro Animation Standards"
8. **Migration path plugin** — PluginManager mencari di `plugins/{slug}/migrations` ATAU `plugins/{slug}/database/migrations` (auto-detect). Plugin baru bebas pakai salah satu, tapi WAJIB konsisten dalam satu plugin
9. **Sub-menu sidebar** — plugin wajib isi `children` di `MenuManager::add()` agar muncul di sidebar combo layout. Tanpa `children`, modul hanya muncul di top navbar
10. **Boot timing** — `MenuManager::add()` WAJIB dibungkus `$this->app->booted(function () { ... })` di dalam guard `if (app()->runningInConsole()) return;`. Ini memastikan `route()` dipanggil setelah semua provider selesai boot dan routing fully committed di Laravel 12
11. **Core plugin** — plugin bawaan sistem ditandai `is_core = true` di tabel `plugins`. Plugin core tidak bisa di-uninstall/deactivate dari UI. Daftarkan via `CorePluginSeeder`, bukan via Plugin Manager
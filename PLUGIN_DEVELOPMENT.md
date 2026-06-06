# Panduan Membuat Plugin ERP

Panduan lengkap dari scaffold awal sampai rilis plugin ke marketplace.

---

## Daftar Isi

1. [Konsep Dasar](#1-konsep-dasar)
2. [Quick Start — plugin:make](#2-quick-start--pluginmake)
3. [Setup Repository](#3-setup-repository)
4. [Struktur Plugin](#4-struktur-plugin)
5. [Anatomy Plugin (Manual)](#5-anatomy-plugin-manual)
6. [RBAC — Permission & Menu Visibility](#6-rbac--permission--menu-visibility)
7. [Sidebar Menu — Collapsible Sub-menu](#7-sidebar-menu--collapsible-sub-menu)
8. [Mendaftarkan ke Marketplace](#8-mendaftarkan-ke-marketplace)
9. [Setup GitHub Actions](#9-setup-github-actions)
10. [Rilis Plugin](#10-rilis-plugin)
11. [Referensi Cepat](#11-referensi-cepat)

---

## 1. Konsep Dasar

Plugin adalah modul independen yang extend fungsionalitas ERP tanpa menyentuh core app. Setiap plugin adalah **ServiceProvider Laravel** yang di-load saat boot.

**Alur hidup plugin:**
```
Scaffold dengan plugin:make → Coding → composer dump-autoload
    → Activate via Plugin Manager → Migration otomatis jalan
    → Plugin aktif: routes, views, menu terdaftar
```

**Dua tipe plugin:**

| Tipe | Keterangan |
|---|---|
| **Core** | Bawaan sistem (`is_core = true`), tidak bisa uninstall. Contoh: `users`, `masterdata` |
| **Optional** | Bisa install/uninstall user lewat Plugin Manager. Contoh: `accounting`, `inventory` |

**Master data khusus** tetap di plugin pemiliknya — jangan gabungkan ke `masterdata` kecuali dipakai oleh 2+ plugin berbeda.

---

## 2. Quick Start — plugin:make

Cara tercepat scaffold plugin baru:

```bash
C:\sdk\php85\php.exe artisan plugin:make {slug}

# Contoh:
C:\sdk\php85\php.exe artisan plugin:make sales-order
C:\sdk\php85\php.exe artisan plugin:make hr
C:\sdk\php85\php.exe artisan plugin:make crm
```

Command akan tanya:
- Display name (default: prettified slug)
- Description
- Author
- Tabler icon (contoh: `ti ti-shopping-cart`)
- Menu order (angka urutan di top navbar)

Lalu generate **9 file sekaligus**:

```
plugins/{slug}/
├── plugin.json                          ← manifest + permissions
├── Plugin.php                           ← ServiceProvider + menu
├── routes.php                           ← CRUD routes + can: middleware
├── Controllers/{Name}Controller.php     ← CRUD controller siap pakai
├── Models/{Name}.php                    ← Eloquent model
├── migrations/{ts}_create_{table}.php   ← migration dengan up() & down()
└── resources/views/
    ├── index.blade.php                  ← table + dropdown actions
    ├── create.blade.php                 ← form + Alpine loading state
    └── edit.blade.php                   ← form + Alpine loading state
```

**Setelah plugin:make, jalankan:**

```bash
composer85 dump-autoload
```

Lalu activate plugin via Plugin Manager di browser — migration otomatis jalan saat activate.

### Slug dengan hyphen

Slug `sales-order` otomatis dikonversi ke namespace `SalesOrder` (PascalCase). Folder tetap `plugins/sales-order/`.

```
sales-order  →  folder: plugins/sales-order/
             →  namespace: Plugins\SalesOrder
             →  route prefix: sales-order
             →  view namespace: sales-order::
```

Autoloading untuk hyphenated slug ditangani oleh **classmap** di `composer.json` — composer scan seluruh PHP file di `plugins/` tanpa peduli nama folder. Ini kenapa `composer85 dump-autoload` wajib dijalankan setelah scaffold plugin baru.

---

## 3. Setup Repository

Untuk plugin yang akan di-publish ke marketplace, buat repo GitHub terpisah.
Ada dua flow tergantung dari mana kamu mulai:

### Flow A — Submodule dulu, baru coding

Pakai ini jika repo sudah ada di GitHub sebelum mulai coding.

```bash
# 1. Buat repo erp-plugin-{slug} di GitHub (boleh kosong)

# 2. DARI ROOT erp-app — tambah sebagai submodule
git submodule add https://github.com/{username}/erp-plugin-{slug}.git plugins/{slug}
composer85 dump-autoload

# 3. Develop di dalam submodule
cd plugins/{slug}
# edit/buat files...
git add . && git commit -m "feat: ..." && git push

# 4. Update pointer di repo utama
cd ../..
git add plugins/{slug}
git commit -m "update {slug} plugin"
git push
```

### Flow B — plugin:make dulu, baru jadikan submodule

Pakai ini jika sudah scaffold dengan `plugin:make` dan baru mau publish ke marketplace.

```bash
# 1. Buat repo erp-plugin-{slug} di GitHub (kosong)

# 2. Init git DI DALAM folder plugin — bukan dari root erp-app
cd plugins/{slug}
git init
git add .
git commit -m "initial scaffold"
git branch -M main
git remote add origin https://github.com/{username}/erp-plugin-{slug}.git
git push -u origin main

# 3. Kembali ke ROOT erp-app
cd ../..

# 4. Hapus dari tracking langsung erp-app
git rm -r plugins/{slug}

# 5. Tambah sebagai submodule
git submodule add https://github.com/{username}/erp-plugin-{slug}.git plugins/{slug}
git commit -m "refactor: convert {slug} to git submodule"
git push
```

> ⚠️ **Peringatan:** `git submodule add` **wajib** dijalankan dari **root `erp-app/`**, bukan dari dalam `plugins/`. Menjalankan dari `plugins/` akan membuat path ganda `plugins/plugins/{slug}/` yang salah.
>
> ⚠️ **Peringatan:** `plugin:make` hanya buat file, **tidak** init git repo. Jika kamu langsung `git add` dari dalam `plugins/{slug}/` tanpa `git init` dulu, perintah git akan berjalan di konteks `erp-app` (git traversal ke parent), bukan di repo plugin.

### Develop setelah submodule terpasang

```bash
cd plugins/{slug}
# edit files...
git add . && git commit -m "feat: ..." && git push

# Update pointer di repo utama
cd ../..
git add plugins/{slug}
git commit -m "update {slug} plugin"
git push
```

---

## 4. Struktur Plugin

```
plugins/{slug}/
├── plugin.json                 # Manifest (wajib)
├── Plugin.php                  # Entry point ServiceProvider (wajib)
├── routes.php                  # Definisi routes
├── Controllers/
│   └── {Name}Controller.php
├── Models/
│   └── {Name}.php
├── migrations/                 # Atau database/migrations/ — pilih salah satu, konsisten
│   └── YYYY_MM_DD_HHMMSS_create_{table}_table.php
└── resources/
    └── views/
        └── index.blade.php
```

---

## 5. Anatomy Plugin (Manual)

### 5a. plugin.json

```json
{
    "name": "Human Resources",
    "slug": "hr",
    "version": "1.0.0",
    "description": "Manajemen karyawan, absensi, dan penggajian",
    "author": "username",
    "depends": [],
    "permissions": [
        {"name": "hr.view",   "label": "View HR"},
        {"name": "hr.manage", "label": "Manage HR"}
    ]
}
```

| Field | Keterangan |
|---|---|
| `slug` | Unik, lowercase. Dipakai sebagai namespace dan URL prefix |
| `version` | Semver: `MAJOR.MINOR.PATCH` |
| `depends` | Slug plugin lain yang harus aktif duluan |
| `permissions` | Permission yang di-seed otomatis saat plugin diaktifkan |

---

### 5b. Plugin.php (Entry Point)

```php
<?php

namespace Plugins\hr;

use App\Core\MenuManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class Plugin extends ServiceProvider
{
    public function boot(): void
    {
        // Guard wajib — cegah route() dipanggil saat artisan console
        if (app()->runningInConsole()) return;

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'hr');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        // WAJIB ['web', 'auth'] — bukan hanya 'web', agar session & $errors tersedia
        Route::middleware(['web', 'auth'])->group(__DIR__ . '/routes.php');

        // Menu WAJIB dibungkus app->booted() — route() baru tersedia setelah semua provider selesai boot
        $this->app->booted(function () {
            app()->make(MenuManager::class)->add([
                'title'      => 'HR',
                'url'        => route('hr.employees.index'),
                'icon'       => 'ti ti-users',
                'order'      => 30,
                'active'     => 'hr*',
                'permission' => 'hr.view',        // null = tampil untuk semua user login
                'children'   => [
                    // Lihat Seksi 7 untuk format collapsible sub-menu
                ],
            ]);
        });
    }
}
```

**Aturan kritis:**
- `if (app()->runningInConsole()) return;` — wajib, atau `composer dump-autoload` crash
- `Route::middleware(['web', 'auth'])` — wajib dua-duanya, bukan hanya `'web'`
- `app()->booted(function () { ... })` — wajib untuk menu, atau `route()` belum tersedia di Laravel 12

---

### 5c. routes.php

Static route HARUS didaftarkan sebelum dynamic route — cegah `create` tercocok sebagai `{id}`:

```php
<?php

use Illuminate\Support\Facades\Route;
use Plugins\hr\Controllers\EmployeeController;

Route::prefix('hr')->name('hr.')->group(function () {
    // Static dulu
    Route::get('employees',              [EmployeeController::class, 'index'])->name('employees.index')->middleware('can:hr.view');
    Route::get('employees/create',       [EmployeeController::class, 'create'])->name('employees.create')->middleware('can:hr.manage');
    Route::post('employees',             [EmployeeController::class, 'store'])->name('employees.store')->middleware('can:hr.manage');
    // Dynamic belakangan
    Route::get('employees/{employee}',      [EmployeeController::class, 'show'])->name('employees.show')->middleware('can:hr.view');
    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit')->middleware('can:hr.manage');
    Route::put('employees/{employee}',      [EmployeeController::class, 'update'])->name('employees.update')->middleware('can:hr.manage');
    Route::delete('employees/{employee}',   [EmployeeController::class, 'destroy'])->name('employees.destroy')->middleware('can:hr.manage');
});
```

---

### 5d. Controller

```php
<?php

namespace Plugins\hr\Controllers;

use App\Http\Controllers\Controller;
use Plugins\hr\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::latest()->paginate(20);
        return view('hr::employees.index', compact('employees'));
        //           ^^^^ namespace blade — 'hr::' merujuk ke resources/views/
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
        ]);

        Employee::create($request->validated());

        return redirect()->route('hr.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }
}
```

---

### 5e. Model

```php
<?php

namespace Plugins\hr\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'position', 'joined_at'];
}
```

---

### 5f. Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->date('joined_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
```

`down()` wajib ada dan benar — dipakai saat user uninstall dengan opsi "Hapus Data". Jika ada FK, drop child table dulu:

```php
public function down(): void
{
    Schema::dropIfExists('employee_attendances'); // child dulu
    Schema::dropIfExists('employees');            // baru parent
}
```

---

### 5g. View (Blade)

```blade
@extends('layouts.app')

@section('title', 'Employees')
@section('page-title', 'Employees')

@section('page-actions')
@can('hr.manage')
<a href="{{ route('hr.employees.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-1"></i>Add Employee
</a>
@endcan
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr><th>Name</th><th>Position</th><th class="w-1"></th></tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($employees as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->position }}</td>
                    <td>
                        @can('hr.manage')
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('hr.employees.edit', $employee) }}" class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                                <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST"
                                      onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger">
                                        <i class="ti ti-trash me-2"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
```

Lihat [CLAUDE.md](CLAUDE.md) untuk standar UI component dan micro animation lengkap.

---

## 6. RBAC — Permission & Menu Visibility

ERP ini menggunakan **2-layer RBAC** (WordPress/Odoo style):

| Layer | Mekanisme | Efek |
|---|---|---|
| **Route** | `->middleware('can:hr.view')` | 403 jika akses tanpa permission |
| **Menu** | `'permission' => 'hr.view'` di MenuManager | Menu disembunyikan di navbar/sidebar |

**Role `admin` otomatis bypass semua permission** via `Gate::before()` di AppServiceProvider.

### Mendaftarkan permission di plugin.json

```json
"permissions": [
    {"name": "hr.view",   "label": "View HR"},
    {"name": "hr.manage", "label": "Manage HR"}
]
```

Permission di-seed otomatis saat plugin diaktifkan. Admin mendapat semua permission saat `AdminSeeder` dijalankan.

### Menggunakan permission di view

```blade
@can('hr.manage')
    <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">Add</a>
@endcan
```

### Konvensi penamaan permission

```
{slug}.view    — baca/lihat data
{slug}.manage  — create, edit, delete
{slug}.export  — export data (opsional)
{slug}.approve — approve workflow (opsional)
```

---

## 7. Sidebar Menu — Collapsible Sub-menu

Sidebar mendukung **3 level** hierarki:

```
Top Navbar  →  Modul (Accounting, HR, dll)
Sidebar L1  →  Section (collapsible, punya icon + chevron)
Sidebar L2  →  Sub-item (indent, tanpa icon, bg-highlight saat active)
```

### Contoh struktur menu 3 level

```php
$this->app->booted(function () {
    app()->make(MenuManager::class)->add([
        // Level 1 — Top Navbar
        'title'      => 'HR',
        'url'        => route('hr.employees.index'),
        'icon'       => 'ti ti-users',
        'order'      => 30,
        'active'     => 'hr*',
        'permission' => 'hr.view',
        'children'   => [

            // Level 2 — Sidebar section (collapsible)
            [
                'title'      => 'Employees',
                'icon'       => 'ti ti-user',
                'active'     => 'hr/employees*',
                'permission' => 'hr.view',
                'children'   => [

                    // Level 3 — Sub-item (link langsung)
                    ['title' => 'All Employees', 'url' => route('hr.employees.index'),  'active' => 'hr/employees'],
                    ['title' => 'Add Employee',  'url' => route('hr.employees.create'), 'active' => 'hr/employees/create', 'permission' => 'hr.manage'],

                ],
            ],

            [
                'title'      => 'Attendance',
                'icon'       => 'ti ti-calendar',
                'active'     => 'hr/attendance*',
                'permission' => 'hr.view',
                'children'   => [
                    ['title' => 'All Records', 'url' => route('hr.attendance.index'), 'active' => 'hr/attendance'],
                    ['title' => 'Add Record',  'url' => route('hr.attendance.create'), 'active' => 'hr/attendance/create', 'permission' => 'hr.manage'],
                ],
            ],

        ],
    ]);
});
```

**Aturan:**
- Section tanpa `children` → flat link biasa di sidebar
- Section dengan `children` → collapsible accordion, expand otomatis jika URL aktif
- Sub-item tidak perlu `icon` — hirarki dibedakan dari indentasi dan warna saja
- `permission` di level 2 (section) menyembunyikan seluruh section beserta sub-item-nya

---

## 8. Mendaftarkan ke Marketplace

Registry ini **internal** — hanya developer yang sudah diizinkan maintainer yang bisa publish plugin.

Plugin otomatis muncul di marketplace saat buat GitHub Release pertama — tidak perlu daftar manual, asalkan workflow dan `REGISTRY_TOKEN` sudah terpasang.

Untuk bergabung sebagai plugin developer: hubungi maintainer ERP untuk mendapatkan `REGISTRY_TOKEN`.

---

## 9. Setup GitHub Actions

### Buat workflow di repo plugin

Buat file `.github/workflows/publish.yml`:

```yaml
name: Publish to Registry

on:
  release:
    types: [published]

jobs:
  publish:
    runs-on: ubuntu-latest
    steps:
      - name: Dispatch to ERP Registry
        run: |
          curl -X POST \
            -H "Authorization: Bearer ${{ secrets.REGISTRY_TOKEN }}" \
            -H "Accept: application/vnd.github.v3+json" \
            https://api.github.com/repos/febriandto/erp-plugin-registry/dispatches \
            -d "{
              \"event_type\": \"plugin-release\",
              \"client_payload\": {
                \"github_url\": \"https://github.com/${{ github.repository }}\",
                \"download_url\": \"https://github.com/${{ github.repository }}/archive/refs/tags/${{ github.ref_name }}.zip\"
              }
            }"
```

### Tambah REGISTRY_TOKEN secret

Minta `REGISTRY_TOKEN` ke maintainer ERP, lalu tambahkan di repo plugin:

**Settings → Secrets and variables → Actions → New repository secret**

- Name: `REGISTRY_TOKEN`
- Value: *(token dari maintainer)*

> **Untuk maintainer:** Token ini adalah fine-grained PAT dengan permission **"Actions: write"** hanya pada repo `erp-plugin-registry`. Jangan embed di file yang dipush ke public repo — GitHub Secret Scanning akan otomatis revoke.

---

## 10. Rilis Plugin

### Rilis pertama (v1.0.0)

```bash
git add .
git commit -m "feat: initial release"
git push
```

Buat GitHub Release:
1. Buka repo plugin di GitHub → **Releases → Create a new release**
2. Tag: `v1.0.0` | Target: `main`
3. Klik **Publish release** — GitHub Actions otomatis update registry

### Rilis update (v1.0.1, v1.1.0, dst)

```bash
# 1. Update versi di plugin.json
# 2. Commit dan push
git add . && git commit -m "feat: tambah fitur X" && git push
# 3. Buat GitHub Release dengan tag versi baru
#    → Actions update registry → user klik Update di Plugin Manager
```

**Versioning:**

| Jenis perubahan | Bump |
|---|---|
| Bug fix, typo | `1.0.0` → `1.0.1` |
| Fitur baru, backward-compatible | `1.0.0` → `1.1.0` |
| Breaking change, ubah struktur tabel | `1.0.0` → `2.0.0` |

---

## 11. Referensi Cepat

### Checklist plugin baru

- [ ] Jalankan `plugin:make {slug}` untuk scaffold boilerplate
- [ ] Sesuaikan `plugin.json` — tambah `depends` jika butuh plugin lain
- [ ] Tambah permission di `plugin.json` sesuai kebutuhan
- [ ] Tambah kolom di migration sesuai kebutuhan domain
- [ ] Tambah field di `$fillable` model
- [ ] Update validation di controller (store & update)
- [ ] Tambah kolom di view (index, create, edit)
- [ ] Konfigurasi menu 3-level di `Plugin.php` (`children` dengan `children`)
- [ ] Jalankan `composer85 dump-autoload`
- [ ] Activate via Plugin Manager — cek migration jalan
- [ ] Test akses dengan role yang punya permission dan yang tidak

### Checklist sebelum publish ke marketplace

- [ ] Buat repo `erp-plugin-{slug}` di GitHub
- [ ] Setup `.github/workflows/publish.yml`
- [ ] Tambah `REGISTRY_TOKEN` secret ke repo
- [ ] Versi di `plugin.json` sudah benar
- [ ] `down()` di migration sudah benar
- [ ] Buat GitHub Release → test install dari Plugin Manager

### Namespace cheatsheet

```php
// Slug tanpa hyphen (hr, accounting, inventory)
namespace Plugins\hr;
namespace Plugins\hr\Controllers;
namespace Plugins\hr\Models;

// Slug dengan hyphen (sales-order → SalesOrder)
namespace Plugins\SalesOrder;
namespace Plugins\SalesOrder\Controllers;
namespace Plugins\SalesOrder\Models;

// View (di controller) — selalu pakai slug as-is
return view('hr::employees.index');
return view('sales-order::orders.index');

// Route name — selalu pakai slug as-is
route('hr.employees.index');
route('sales-order.index');
```

### Artisan commands

```bash
C:\sdk\php85\php.exe artisan plugin:make {slug}    # scaffold plugin baru
C:\sdk\php85\php.exe artisan plugin:list            # list semua plugin
C:\sdk\php85\php.exe artisan plugin:activate {slug}
C:\sdk\php85\php.exe artisan plugin:deactivate {slug}
C:\sdk\php85\php.exe artisan migrate               # jalankan migration
composer85 dump-autoload                            # update autoloader
```

### Icon

Gunakan Tabler Icons: https://tabler.io/icons — format: `ti ti-{name}`

```html
<i class="ti ti-users"></i>
<i class="ti ti-shopping-cart"></i>
<i class="ti ti-chart-bar"></i>
<i class="ti ti-file-invoice"></i>
```

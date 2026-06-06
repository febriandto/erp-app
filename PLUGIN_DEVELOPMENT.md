# Panduan Membuat Plugin ERP

Panduan lengkap dari setup awal sampai rilis plugin ke marketplace.

---

## Daftar Isi

1. [Konsep Dasar](#1-konsep-dasar)
2. [Setup Repository](#2-setup-repository)
3. [Struktur Plugin](#3-struktur-plugin)
4. [Membuat Plugin](#4-membuat-plugin)
5. [Mendaftarkan ke Marketplace](#5-mendaftarkan-ke-marketplace)
6. [Setup GitHub Actions](#6-setup-github-actions)
7. [Rilis Plugin](#7-rilis-plugin)
8. [Referensi Cepat](#8-referensi-cepat)

---

## 1. Konsep Dasar

Plugin adalah modul independen yang extend fungsionalitas ERP tanpa menyentuh core app. Setiap plugin adalah **repo GitHub tersendiri** yang di-install user via Plugin Manager.

**Alur hidup plugin:**
```
Buat repo GitHub → Coding plugin → Push → Daftar ke registry
    → User install via Plugin Manager (download ZIP otomatis)
    → Buat release baru → GitHub Actions update registry → User klik Update
```

**Konvensi nama repo:** `erp-plugin-{slug}` — contoh: `erp-plugin-hr`, `erp-plugin-crm`

---

## 2. Setup Repository

### 2a. Buat repo di GitHub

Buat repo baru dengan nama `erp-plugin-{slug}` (public).

### 2b. Clone dan init struktur

```bash
git clone https://github.com/{username}/erp-plugin-{slug}.git
cd erp-plugin-{slug}
```

### 2c. Tambahkan plugin ke erp-app untuk development

```bash
# Di root erp-app
git submodule add https://github.com/{username}/erp-plugin-{slug}.git plugins/{slug}
composer dump-autoload
```

---

## 3. Struktur Plugin

```
plugins/{slug}/
├── plugin.json                    # Manifest (wajib)
├── Plugin.php                     # Entry point (wajib)
├── routes.php                     # Definisi routes
├── Controllers/
│   └── {Name}Controller.php
├── Models/
│   └── {Name}.php
├── database/
│   └── migrations/
│       └── 2024_01_01_000001_create_{table}_table.php
└── resources/
    └── views/
        └── {feature}/
            ├── index.blade.php
            ├── create.blade.php
            └── show.blade.php
```

---

## 4. Membuat Plugin

### 4a. plugin.json

```json
{
    "name": "Human Resources",
    "slug": "hr",
    "version": "1.0.0",
    "description": "Manajemen karyawan, absensi, dan penggajian",
    "author": "username",
    "depends": []
}
```

| Field | Keterangan |
|---|---|
| `slug` | Harus unik, huruf kecil, tanpa spasi. Dipakai sebagai namespace dan URL prefix |
| `version` | Ikuti semver: `MAJOR.MINOR.PATCH` |
| `depends` | Slug plugin lain yang harus terinstall duluan |

---

### 4b. Plugin.php (Entry Point)

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
        // Views — namespace: 'hr'
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'hr');

        // Migrations — cek dua lokasi yang didukung:
        // Option A: plugins/hr/migrations/
        // Option B: plugins/hr/database/migrations/
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Routes — WAJIB pakai middleware('web'), bukan loadRoutesFrom()
        Route::middleware('web')->group(__DIR__ . '/routes.php');

        // Daftarkan di menu — muncul di top navbar dan sidebar
        $this->app->make(MenuManager::class)->add([
            'title'  => 'HR',
            'url'    => route('hr.employees.index'),
            'icon'   => 'ti ti-users',        // dari tabler.io/icons
            'order'  => 30,                   // urutan di top navbar
            'active' => 'hr*',                // pattern URL aktif
            'children' => [
                // Sub-menu muncul di sidebar saat modul HR aktif
                ['title' => 'Employees', 'url' => route('hr.employees.index'), 'icon' => 'ti ti-user', 'active' => 'hr/employees*'],
                ['title' => 'Attendance', 'url' => route('hr.attendance.index'), 'icon' => 'ti ti-calendar', 'active' => 'hr/attendance*'],
            ],
        ]);
    }
}
```

**Aturan penting:**
- Namespace: `Plugins\{slug}` — bukan `App\Modules\...`
- `loadMigrationsFrom` hanya register path, **tidak** otomatis jalankan migration
- Migration dijalankan saat user klik **Activate** di Plugin Manager

---

### 4c. routes.php

```php
<?php

use Illuminate\Support\Facades\Route;
use Plugins\hr\Controllers\EmployeeController;
use Plugins\hr\Controllers\AttendanceController;

Route::prefix('hr')->name('hr.')->group(function () {
    Route::resource('employees', EmployeeController::class);
    Route::resource('attendance', AttendanceController::class);
});
```

---

### 4d. Controller

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

### 4e. Model

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

### 4f. Migration

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

**Catatan:** Method `down()` wajib ada dan benar — dipakai saat user uninstall dengan opsi "Hapus Data".

Jika ada foreign key antar tabel, drop dalam urutan terbalik:
```php
public function down(): void
{
    Schema::dropIfExists('employee_attendances'); // child dulu
    Schema::dropIfExists('employees');            // baru parent
}
```

---

### 4g. View (Blade)

```blade
{{-- plugins/hr/resources/views/employees/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Employees')
@section('page-title', 'Employees')

@section('page-actions')
    <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Add Employee
    </a>
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($employees as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->position }}</td>
                    <td>
                        <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST"
                              x-data="{ loading: false }" @submit="loading = true">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <span x-show="!loading">Delete</span>
                                <span x-show="loading" x-cloak>
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">Belum ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
```

Lihat [CLAUDE.md](CLAUDE.md) untuk standar UI component dan micro animation.

---

## 5. Mendaftarkan ke Marketplace

**Tidak perlu daftar manual.** Plugin otomatis muncul di marketplace saat kamu buat GitHub Release pertama kali — selama `plugin.json` sudah ada dan workflow sudah terpasang.

---

## 6. Setup GitHub Actions

### 6a. Buat workflow di repo plugin

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

Satu step — kirim sinyal ke registry. Registry yang akan fetch `plugin.json` dan update dirinya sendiri.

### 6b. Tambah REGISTRY_TOKEN secret

`REGISTRY_TOKEN` adalah token yang disediakan oleh pemilik registry. Dapatkan dari dokumentasi atau hubungi maintainer ERP.

Di repo plugin: **Settings → Secrets and variables → Actions → New repository secret**

- Name: `REGISTRY_TOKEN`
- Value: *(token yang didapat dari maintainer)*

> **Untuk maintainer registry:** Buat fine-grained PAT di GitHub dengan permission **"Actions: write"** hanya pada repo `erp-plugin-registry`. Share token ini ke developer plugin. Token ini hanya bisa trigger workflow, tidak bisa baca/tulis kode.

---

## 7. Rilis Plugin

### Rilis pertama (v1.0.0)

```bash
# Pastikan plugin.json sudah versi 1.0.0
git add .
git commit -m "feat: initial release"
git push
```

Buat GitHub Release:
1. Buka repo plugin di GitHub
2. **Releases → Create a new release**
3. Tag: `v1.0.0` → Target: `main`
4. Title: `v1.0.0`
5. Klik **Publish release**

GitHub Actions otomatis update `registry.json` dengan `download_url`. User bisa install dari Plugin Manager.

---

### Rilis update (v1.0.1, v1.1.0, dst)

```bash
# 1. Update versi di plugin.json
# "version": "1.0.1"

# 2. Commit dan push perubahan
git add .
git commit -m "feat: tambah fitur X"
git push

# 3. Buat GitHub Release dengan tag versi baru
#    → Actions otomatis update registry
#    → User yang sudah install melihat badge "Update Available"
#    → User klik Update → ZIP baru di-download otomatis
```

**Aturan versioning:**
| Jenis perubahan | Contoh | Bump |
|---|---|---|
| Bug fix, typo | Fix kalkulasi total | `1.0.0` → `1.0.1` |
| Fitur baru, backward-compatible | Tambah halaman Reports | `1.0.0` → `1.1.0` |
| Breaking change, restrukturisasi besar | Ubah struktur tabel utama | `1.0.0` → `2.0.0` |

---

## 8. Referensi Cepat

### Checklist plugin baru

- [ ] Buat repo `erp-plugin-{slug}` di GitHub (public)
- [ ] Buat `plugin.json` dengan slug unik
- [ ] Buat `Plugin.php` dengan namespace `Plugins\{slug}`
- [ ] Routes pakai `Route::middleware('web')->group(...)`
- [ ] Migration punya method `down()` yang benar
- [ ] Menu terdaftar via `MenuManager::add()` dengan `children`
- [ ] Setup `.github/workflows/publish.yml` (dispatch ke registry)
- [ ] Tambah `REGISTRY_TOKEN` secret di repo plugin
- [ ] Buat GitHub Release → test install dari Plugin Manager

### Namespace cheatsheet

```php
// Entry point
namespace Plugins\hr;

// Controller
namespace Plugins\hr\Controllers;

// Model
namespace Plugins\hr\Models;

// View (di controller)
return view('hr::employees.index');

// Route name
route('hr.employees.index')
```

### Icon

Gunakan Tabler Icons: https://tabler.io/icons — format: `ti ti-{name}`

```html
<i class="ti ti-users"></i>
<i class="ti ti-calendar"></i>
<i class="ti ti-chart-bar"></i>
```

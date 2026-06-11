@extends('layouts.app')

@section('title', 'License Manager — Demo')
@section('page-title', 'License Manager')

@section('page-actions')
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-issue-license">
    <i class="ti ti-key me-1"></i>Issue License
</button>
@endsection

@php
// ─── DUMMY DATA ───────────────────────────────────────────────────────────────

$scopes = [
    ['id' => 1, 'name' => 'HR Module',         'slug' => 'hr',         'identifier' => 'com.erpapp.plugin.hr',         'key_rotation_days' => 90,  'default_max_usages' => 1],
    ['id' => 2, 'name' => 'Accounting Module', 'slug' => 'accounting', 'identifier' => 'com.erpapp.plugin.accounting', 'key_rotation_days' => 90,  'default_max_usages' => 1],
    ['id' => 3, 'name' => 'Inventory Module',  'slug' => 'inventory',  'identifier' => 'com.erpapp.plugin.inventory',  'key_rotation_days' => 180, 'default_max_usages' => 3],
    ['id' => 4, 'name' => 'Purchasing Module', 'slug' => 'purchasing', 'identifier' => 'com.erpapp.plugin.purchasing', 'key_rotation_days' => 90,  'default_max_usages' => 1],
];

// Data dikelompokkan per client
$clients = [
    [
        'id'      => 1,
        'name'    => 'PT Maju Bersama',
        'email'   => 'it@majubersama.co.id',
        'licenses' => [
            ['id' => 1, 'key_preview' => 'HRMD-****-****-A1B2', 'scope' => 'HR Module',         'scope_slug' => 'hr',         'max_usages' => 1, 'used_seats' => 1, 'expires_at' => null,         'maintenance_end' => '2027-06-07', 'status' => 'active',  'issued_at' => '2026-06-07'],
            ['id' => 2, 'key_preview' => 'ACCT-****-****-B2C3', 'scope' => 'Accounting Module', 'scope_slug' => 'accounting', 'max_usages' => 1, 'used_seats' => 1, 'expires_at' => null,         'maintenance_end' => '2027-06-07', 'status' => 'active',  'issued_at' => '2026-06-07'],
            ['id' => 3, 'key_preview' => 'INVT-****-****-C3D4', 'scope' => 'Inventory Module',  'scope_slug' => 'inventory',  'max_usages' => 1, 'used_seats' => 0, 'expires_at' => null,         'maintenance_end' => '2027-06-07', 'status' => 'pending', 'issued_at' => '2026-06-07'],
        ],
    ],
    [
        'id'      => 2,
        'name'    => 'CV Teknologi Nusantara',
        'email'   => 'admin@teknologiNusantara.com',
        'licenses' => [
            ['id' => 4, 'key_preview' => 'ACCT-****-****-D4E5', 'scope' => 'Accounting Module', 'scope_slug' => 'accounting', 'max_usages' => 1, 'used_seats' => 0, 'expires_at' => null,         'maintenance_end' => '2027-01-15', 'status' => 'pending',   'issued_at' => '2026-05-20'],
            ['id' => 5, 'key_preview' => 'PRCG-****-****-E5F6', 'scope' => 'Purchasing Module', 'scope_slug' => 'purchasing', 'max_usages' => 1, 'used_seats' => 0, 'expires_at' => null,         'maintenance_end' => '2027-01-15', 'status' => 'pending',   'issued_at' => '2026-05-20'],
        ],
    ],
    [
        'id'      => 3,
        'name'    => 'PT Distributor Utama',
        'email'   => 'erp@distributor.com',
        'licenses' => [
            ['id' => 6, 'key_preview' => 'INVT-****-****-F6G7', 'scope' => 'Inventory Module',  'scope_slug' => 'inventory',  'max_usages' => 3, 'used_seats' => 3, 'expires_at' => '2025-12-31', 'maintenance_end' => '2025-12-31', 'status' => 'expired',   'issued_at' => '2024-12-31'],
            ['id' => 7, 'key_preview' => 'PRCG-****-****-G7H8', 'scope' => 'Purchasing Module', 'scope_slug' => 'purchasing', 'max_usages' => 3, 'used_seats' => 3, 'expires_at' => '2025-12-31', 'maintenance_end' => '2025-12-31', 'status' => 'expired',   'issued_at' => '2024-12-31'],
        ],
    ],
    [
        'id'      => 4,
        'name'    => 'PT Sinar Abadi',
        'email'   => 'sysadmin@sinarabadi.id',
        'licenses' => [
            ['id' => 8, 'key_preview' => 'HRMD-****-****-H8I9', 'scope' => 'HR Module',         'scope_slug' => 'hr',         'max_usages' => 1, 'used_seats' => 1, 'expires_at' => null,         'maintenance_end' => '2026-03-01', 'status' => 'suspended', 'issued_at' => '2025-03-01'],
        ],
    ],
];

// Flatten untuk stats
$allLicenses = collect($clients)->flatMap(fn($c) => $c['licenses']);
$stats = [
    'clients'   => count($clients),
    'licenses'  => $allLicenses->count(),
    'active'    => $allLicenses->where('status', 'active')->count(),
    'expired'   => $allLicenses->whereIn('status', ['expired', 'suspended'])->count(),
];

$usages = [
    ['client_id' => 1, 'client' => 'PT Maju Bersama',      'scope' => 'HR Module',        'device' => 'erp.majubersama.co.id',     'fingerprint' => 'sha256:a1b2c3d4...', 'registered_at' => '2026-06-07 09:12', 'last_verified' => '2026-06-07 14:30'],
    ['client_id' => 1, 'client' => 'PT Maju Bersama',      'scope' => 'Accounting Module','device' => 'erp.majubersama.co.id',     'fingerprint' => 'sha256:b2c3d4e5...', 'registered_at' => '2026-06-07 09:15', 'last_verified' => '2026-06-07 14:30'],
    ['client_id' => 3, 'client' => 'PT Distributor Utama', 'scope' => 'Inventory Module', 'device' => 'erp.distributor.com',       'fingerprint' => 'sha256:e5f6g7h8...', 'registered_at' => '2025-01-10 11:00', 'last_verified' => '2025-11-20 08:00'],
    ['client_id' => 3, 'client' => 'PT Distributor Utama', 'scope' => 'Inventory Module', 'device' => 'staging.distributor.com',   'fingerprint' => 'sha256:i9j0k1l2...', 'registered_at' => '2025-02-01 13:30', 'last_verified' => '2025-10-15 10:00'],
    ['client_id' => 3, 'client' => 'PT Distributor Utama', 'scope' => 'Inventory Module', 'device' => 'backup.distributor.com',    'fingerprint' => 'sha256:m3n4o5p6...', 'registered_at' => '2025-03-15 09:00', 'last_verified' => '2025-09-01 07:00'],
];

// Helper closure untuk badge warna per status license
$statusBadge = fn($s) => match($s) {
    'active'    => ['success', 'ti-circle-check', 'Active'],
    'pending'   => ['warning', 'ti-clock',         'Pending'],
    'expired'   => ['danger',  'ti-calendar-x',    'Expired'],
    'suspended' => ['danger',  'ti-ban',            'Suspended'],
    default     => ['secondary','ti-circle',        ucfirst($s)],
};

// Overall status per client: ambil status terburuk
$clientOverallStatus = function($licenses) {
    $all = collect($licenses)->pluck('status');
    if ($all->contains('suspended')) return 'suspended';
    if ($all->contains('expired'))   return 'expired';
    if ($all->contains('pending'))   return 'pending';
    if ($all->every(fn($s) => $s === 'active')) return 'active';
    return 'partial'; // ada yang active ada yang belum
};
@endphp

@section('content')
<div class="anim-fadein">

{{-- Demo Banner --}}
<div class="alert alert-info alert-dismissible mb-4" role="alert">
    <div class="d-flex gap-2">
        <i class="ti ti-flask fs-4 flex-shrink-0 mt-1"></i>
        <div>
            <div class="fw-medium">Halaman demo / preview</div>
            <div class="text-muted small mt-1">
                Semua data adalah <strong>dummy statis</strong> — gambaran tampilan jika mengintegrasikan
                <a href="https://github.com/masterix21/laravel-licensing" target="_blank" class="alert-link">laravel-licensing</a>.
                Tidak ada operasi nyata di sini.
            </div>
        </div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert"></a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4 anim-stagger">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded bg-blue-lt text-blue"><i class="ti ti-building fs-3"></i></div>
                    <div>
                        <div class="text-muted small">Total Clients</div>
                        <div class="fw-bold fs-3">{{ $stats['clients'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded bg-purple-lt text-purple"><i class="ti ti-key fs-3"></i></div>
                    <div>
                        <div class="text-muted small">Total Licenses</div>
                        <div class="fw-bold fs-3">{{ $stats['licenses'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded bg-success-lt text-success"><i class="ti ti-circle-check fs-3"></i></div>
                    <div>
                        <div class="text-muted small">Active Licenses</div>
                        <div class="fw-bold fs-3">{{ $stats['active'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded bg-danger-lt text-danger"><i class="ti ti-alert-circle fs-3"></i></div>
                    <div>
                        <div class="text-muted small">Expired / Suspended</div>
                        <div class="fw-bold fs-3">{{ $stats['expired'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Left: Client grouped license table --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-building me-2 text-muted"></i>Clients & Licenses</h3>
                <div class="card-options">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control form-control-sm" placeholder="Cari client..." style="width:160px">
                    </div>
                </div>
            </div>

            {{-- Per-client accordion --}}
            <div class="list-group list-group-flush">
                @foreach($clients as $client)
                @php
                    $overallStatus = $clientOverallStatus($client['licenses']);
                    [$osc, $osi, $osl] = match($overallStatus) {
                        'active'    => ['success',   'ti-circle-check', 'All Active'],
                        'pending'   => ['warning',   'ti-clock',        'Pending'],
                        'expired'   => ['danger',    'ti-calendar-x',   'Expired'],
                        'suspended' => ['danger',    'ti-ban',          'Suspended'],
                        'partial'   => ['blue',      'ti-circle-half',  'Partial Active'],
                        default     => ['secondary', 'ti-circle',       'Unknown'],
                    };
                    $totalSeatsUsed = collect($client['licenses'])->sum('used_seats');
                    $totalSeatsMax  = collect($client['licenses'])->sum('max_usages');
                    $pluginCount    = count($client['licenses']);
                    $expandId       = 'client-' . $client['id'];
                @endphp

                {{-- Client header row (clickable, expand/collapse) --}}
                <div class="list-group-item list-group-item-action px-3 py-0"
                     x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">

                    {{-- Summary row --}}
                    <div class="d-flex align-items-center gap-3 py-3 cursor-pointer"
                         @click="open = !open" style="cursor:pointer">

                        {{-- Expand icon --}}
                        <i class="ti text-muted flex-shrink-0"
                           :class="open ? 'ti-chevron-down' : 'ti-chevron-right'"
                           style="font-size:.85rem"></i>

                        {{-- Client info --}}
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-medium">{{ $client['name'] }}</div>
                            <div class="text-muted small">{{ $client['email'] }}</div>
                        </div>

                        {{-- Plugin badges ringkas --}}
                        <div class="d-flex gap-1 flex-wrap d-none d-md-flex">
                            @foreach($client['licenses'] as $lic)
                            @php [$lc] = $statusBadge($lic['status']); @endphp
                            <span class="badge bg-{{ $lc }}-lt text-{{ $lc }}" style="font-size:.65rem">
                                {{ explode(' ', $lic['scope'])[0] }}
                            </span>
                            @endforeach
                        </div>

                        {{-- Seats total --}}
                        <div class="text-center flex-shrink-0" style="width:60px">
                            <div class="fw-medium small {{ $totalSeatsUsed >= $totalSeatsMax ? 'text-danger' : 'text-muted' }}">
                                {{ $totalSeatsUsed }}/{{ $totalSeatsMax }}
                            </div>
                            <div class="text-muted" style="font-size:.65rem">seats</div>
                        </div>

                        {{-- Overall status --}}
                        <div class="flex-shrink-0" style="width:110px">
                            <span class="badge bg-{{ $osc }}-lt text-{{ $osc }}">
                                <i class="ti {{ $osi }} me-1"></i>{{ $osl }}
                            </span>
                        </div>

                        {{-- Actions --}}
                        <div class="flex-shrink-0" @click.stop>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-ghost-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#"
                                       data-bs-toggle="modal" data-bs-target="#modal-issue-license">
                                        <i class="ti ti-key me-2"></i>Issue License Baru
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="ti ti-mail me-2"></i>Kirim Email ke Client
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#">
                                        <i class="ti ti-ban me-2"></i>Suspend Semua
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Expanded: per-license detail --}}
                    <div x-show="open" x-collapse class="pb-2">
                        <div class="table-responsive rounded border">
                            <table class="table table-sm table-vcenter mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Plugin / Scope</th>
                                        <th>License Key</th>
                                        <th class="text-center">Seats</th>
                                        <th>Maintenance</th>
                                        <th>Status</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($client['licenses'] as $lic)
                                    @php
                                        [$lc, $li, $ll] = $statusBadge($lic['status']);
                                        $mEnd     = \Carbon\Carbon::parse($lic['maintenance_end']);
                                        $mExpired = $mEnd->isPast();
                                        $full     = $lic['used_seats'] >= $lic['max_usages'] && $lic['max_usages'] > 0;
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-blue-lt text-blue">
                                                <i class="ti ti-puzzle me-1"></i>{{ $lic['scope'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <code class="small text-muted">{{ $lic['key_preview'] }}</code>
                                                <button class="btn btn-sm btn-ghost-secondary py-0 px-1" title="Copy">
                                                    <i class="ti ti-copy" style="font-size:.75rem"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="small {{ $full ? 'text-danger fw-medium' : 'text-muted' }}">
                                                {{ $lic['used_seats'] }}/{{ $lic['max_usages'] }}
                                            </span>
                                            <div class="progress mt-1" style="height:3px;width:50px;margin:0 auto">
                                                <div class="progress-bar {{ $full ? 'bg-danger' : 'bg-success' }}"
                                                     style="width:{{ $lic['max_usages'] > 0 ? ($lic['used_seats']/$lic['max_usages']*100) : 0 }}%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small {{ $mExpired ? 'text-danger' : 'text-muted' }}">
                                                @if($mExpired)
                                                <i class="ti ti-alert-triangle me-1"></i>
                                                @endif
                                                {{ $mEnd->format('d M Y') }}
                                            </div>
                                            @if(!$mExpired)
                                            <div class="text-muted" style="font-size:.68rem">{{ $mEnd->diffForHumans() }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $lc }}-lt text-{{ $lc }}">
                                                <i class="ti {{ $li }} me-1"></i>{{ $ll }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-ghost-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="#"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#modal-detail-{{ $lic['id'] }}">
                                                        <i class="ti ti-eye me-2"></i>Detail & Token
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="ti ti-refresh me-2"></i>Regenerate Key
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        <i class="ti ti-calendar-plus me-2"></i>Renew Maintenance
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    @if($lic['status'] === 'active')
                                                    <a class="dropdown-item text-warning" href="#">
                                                        <i class="ti ti-ban me-2"></i>Suspend
                                                    </a>
                                                    @elseif($lic['status'] === 'suspended')
                                                    <a class="dropdown-item text-success" href="#">
                                                        <i class="ti ti-player-play me-2"></i>Unsuspend
                                                    </a>
                                                    @endif
                                                    <a class="dropdown-item text-danger" href="#">
                                                        <i class="ti ti-trash me-2"></i>Revoke
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
        </div>

        {{-- Registered Seats --}}
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-device-laptop me-2 text-muted"></i>Registered Installations</h3>
                <div class="card-options">
                    <span class="text-muted small">Seat terdaftar per server/domain</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Plugin</th>
                            <th>Domain / Server</th>
                            <th>Fingerprint</th>
                            <th>Last Verified</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usages as $u)
                        <tr>
                            <td class="small fw-medium">{{ $u['client'] }}</td>
                            <td>
                                <span class="badge bg-blue-lt text-blue" style="font-size:.65rem">
                                    {{ explode(' ', $u['scope'])[0] }}
                                </span>
                            </td>
                            <td class="small">{{ $u['device'] }}</td>
                            <td><code class="small text-muted">{{ $u['fingerprint'] }}</code></td>
                            <td class="small text-muted">{{ $u['last_verified'] }}</td>
                            <td>
                                <button class="btn btn-sm btn-ghost-danger" title="Revoke seat ini">
                                    <i class="ti ti-x"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="col-lg-4">

        {{-- License Scopes --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-layers-subtract me-2 text-muted"></i>License Scopes</h3>
                <div class="card-options">
                    <a href="#" class="btn btn-sm btn-ghost-secondary"><i class="ti ti-plus"></i></a>
                </div>
            </div>
            <div class="list-group list-group-flush">
                @foreach($scopes as $scope)
                <div class="list-group-item">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <div class="fw-medium small">{{ $scope['name'] }}</div>
                            <code class="text-muted" style="font-size:.7rem">{{ $scope['identifier'] }}</code>
                            <div class="d-flex gap-2 mt-1">
                                <span class="badge bg-secondary-lt text-secondary" style="font-size:.65rem">
                                    max {{ $scope['default_max_usages'] }} seat
                                </span>
                                <span class="badge bg-secondary-lt text-secondary" style="font-size:.65rem">
                                    rotate {{ $scope['key_rotation_days'] }}d
                                </span>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-ghost-secondary flex-shrink-0">
                            <i class="ti ti-settings" style="font-size:.8rem"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Cara Kerja --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-info-circle me-2 text-muted"></i>Cara Kerja</h3>
            </div>
            <div class="list-group list-group-flush">
                @php
                $steps = [
                    ['icon' => 'ti-cash',         'color' => 'blue',    'title' => '1. Customer Beli Plugin',     'desc' => 'Admin issue license key, dikirim ke client via email.'],
                    ['icon' => 'ti-key',          'color' => 'purple',  'title' => '2. Input License Key',        'desc' => 'Customer masukkan key di Plugin Manager mereka.'],
                    ['icon' => 'ti-server',       'color' => 'blue',    'title' => '3. Validasi ke Server',       'desc' => 'Plugin Manager POST key ke server ini untuk verifikasi + seat check.'],
                    ['icon' => 'ti-download',     'color' => 'success', 'title' => '4. Download Plugin ZIP',      'desc' => 'Jika valid & seat tersedia, server stream ZIP plugin.'],
                    ['icon' => 'ti-shield-check', 'color' => 'success', 'title' => '5. Offline Verify (PASETO)', 'desc' => 'Setiap boot, plugin verify token lokal — tidak butuh internet.'],
                ];
                @endphp
                @foreach($steps as $step)
                <div class="list-group-item py-2">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="p-1 rounded bg-{{ $step['color'] }}-lt text-{{ $step['color'] }} flex-shrink-0" style="line-height:1">
                            <i class="ti {{ $step['icon'] }}" style="font-size:.9rem"></i>
                        </div>
                        <div>
                            <div class="fw-medium small">{{ $step['title'] }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ $step['desc'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- PASETO Token Preview --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="ti ti-shield me-2 text-muted"></i>PASETO Token</h3>
            </div>
            <div class="card-body">
                <div class="text-muted small mb-2">Token yang di-embed di instalasi client:</div>
                <div class="bg-dark rounded p-2 mb-2" style="overflow-x:auto">
                    <code class="text-success" style="font-size:.6rem;word-break:break-all">
                        v4.public.eyJsaWNlbnNlX2lkIjoxLCJzY29wZSI6ImNvbS5lcnBhcHAucGx1Z2luLmhyIiwiY2xpZW50IjoiUFQgTWFqdSBCZXJzYW1hIiwibWF4X3VzYWdlcyI6MSwiZXhwaXJlc19hdCI6bnVsbH0.Ed25519SignatureHere
                    </code>
                </div>
                <div class="text-muted" style="font-size:.72rem">
                    Signed <strong>Ed25519</strong> — tidak bisa dipalsukan tanpa private key server.
                    Verify offline tanpa koneksi ke server.
                </div>
            </div>
        </div>

    </div>
</div>

</div>

{{-- Modal: Issue License --}}
<div class="modal modal-blur fade" id="modal-issue-license" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ti ti-key me-2"></i>Issue New License</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3 small py-2">
                    <i class="ti ti-flask me-1"></i>Demo — form ini tidak submit ke mana-mana.
                </div>
                <div class="mb-3">
                    <label class="form-label required">Client</label>
                    <select class="form-select">
                        @foreach($clients as $c)
                        <option>{{ $c['name'] }}</option>
                        @endforeach
                        <option>+ Client Baru...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Plugin / Scope</label>
                    <select class="form-select">
                        @foreach($scopes as $s)
                        <option value="{{ $s['slug'] }}">{{ $s['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">Max Seats (Instalasi)</label>
                        <input type="number" class="form-control" value="1" min="1">
                        <div class="form-text">Berapa server boleh install.</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Maintenance Until</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 year')) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">License Expires At <span class="text-muted">(opsional)</span></label>
                    <input type="date" class="form-control" placeholder="Kosongkan = perpetual">
                    <div class="form-text">Kosongkan untuk beli putus tanpa expiry.</div>
                </div>
                <div class="mb-0">
                    <label class="form-label">Catatan Internal</label>
                    <textarea class="form-control" rows="2" placeholder="No. invoice, nama sales, dll."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="ti ti-key me-1"></i>Generate & Issue
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modals: License Detail --}}
@foreach($clients as $client)
@foreach($client['licenses'] as $lic)
<div class="modal modal-blur fade" id="modal-detail-{{ $lic['id'] }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $client['name'] }} — {{ $lic['scope'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless mb-3">
                    <tr>
                        <td class="text-muted ps-0" style="width:140px">Client</td>
                        <td class="fw-medium">{{ $client['name'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Plugin</td>
                        <td><span class="badge bg-blue-lt text-blue">{{ $lic['scope'] }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">License Key</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <code class="small">{{ $lic['key_preview'] }}</code>
                                <button class="btn btn-sm btn-ghost-secondary py-0 px-1" title="Reveal">
                                    <i class="ti ti-eye" style="font-size:.8rem"></i>
                                </button>
                                <button class="btn btn-sm btn-ghost-secondary py-0 px-1" title="Copy">
                                    <i class="ti ti-copy" style="font-size:.8rem"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Seats</td>
                        <td>{{ $lic['used_seats'] }} / {{ $lic['max_usages'] }} terpakai</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Maintenance</td>
                        <td class="small">{{ \Carbon\Carbon::parse($lic['maintenance_end'])->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Expires At</td>
                        <td class="small">{{ $lic['expires_at'] ? \Carbon\Carbon::parse($lic['expires_at'])->format('d M Y') : '∞ Perpetual' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Issued</td>
                        <td class="small text-muted">{{ $lic['issued_at'] }}</td>
                    </tr>
                </table>

                <div class="text-muted small mb-1">PASETO Token (di-embed di plugin client):</div>
                <div class="bg-dark rounded p-2">
                    <code class="text-success" style="font-size:.6rem;word-break:break-all">
                        v4.public.{{ base64_encode(json_encode(['id' => $lic['id'], 'scope' => 'com.erpapp.plugin.'.$lic['scope_slug'], 'client' => $client['name'], 'seats' => $lic['max_usages']])) }}.demo_sig
                    </code>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost-secondary me-auto" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-mail me-1"></i>Kirim ke Client
                </button>
                <button type="button" class="btn btn-primary btn-sm">
                    <i class="ti ti-refresh me-1"></i>Regenerate Key
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endforeach

@endsection

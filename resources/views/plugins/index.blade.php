@extends('layouts.app')

@section('title', 'Plugin Manager')
@section('page-title', 'Plugin Manager')

@section('content')

@if($errors->any())
<div class="alert alert-danger alert-dismissible mb-3" role="alert">
    <div>{{ $errors->first() }}</div>
    <a class="btn-close" data-bs-dismiss="alert"></a>
</div>
@endif

{{-- Installed Plugins --}}
<div class="card mb-4 anim-fadein">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-puzzle me-2 text-muted"></i>
            Installed Plugins
        </h3>
        <div class="card-options">
            <span class="badge bg-blue-lt">{{ $installed->count() }} plugins</span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Plugin</th>
                    <th style="width:160px">Version</th>
                    <th style="width:120px">Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($installed as $plugin)
                @php
                    $latestVersion = $latestVersions[$plugin->slug] ?? null;
                    $hasUpdate = $latestVersion && version_compare($latestVersion, $plugin->version, '>');
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <div class="fw-medium">{{ $plugin->name }}</div>
                                <div class="text-muted small">{{ $plugin->description }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="text-muted small fw-medium">v{{ $plugin->version }}</span>
                        @if($hasUpdate)
                        <div class="mt-1">
                            <span class="badge bg-warning-lt text-warning">
                                <i class="ti ti-arrow-up me-1"></i>v{{ $latestVersion }} tersedia
                            </span>
                        </div>
                        @endif
                    </td>
                    <td>
                        @if($plugin->is_active)
                        {{-- Dot indicator lebih halus dari badge solid --}}
                        <span class="d-flex align-items-center gap-1 text-success">
                            <span class="badge bg-success"></span>
                            <span class="small">Active</span>
                        </span>
                        @else
                        <span class="d-flex align-items-center gap-1 text-muted">
                            <span class="badge bg-secondary"></span>
                            <span class="small">Inactive</span>
                        </span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            @if($plugin->is_core)
                                <span class="text-muted small d-flex align-items-center gap-1">
                                    <i class="ti ti-lock" style="font-size:.8rem"></i> Core
                                </span>
                            @else
                                {{-- Update button (muncul kalau ada update tersedia) --}}
                                @if($hasUpdate && $plugin->github_url)
                                <form action="{{ route('plugins.update', $plugin) }}" method="POST"
                                      x-data="{ loading: false }" @submit="loading = true">
                                    @csrf
                                    <input type="hidden" name="download_url" value="{{ $latestDownloadUrls[$plugin->slug] ?? '' }}">
                                    <button class="btn btn-sm btn-warning" type="submit">
                                        <span x-show="!loading"><i class="ti ti-download me-1"></i>Update</span>
                                        <span x-show="loading" x-cloak>
                                            <span class="spinner-border spinner-border-sm me-1"></span>Updating...
                                        </span>
                                    </button>
                                </form>
                                @endif

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        @if($plugin->is_active)
                                        <form action="{{ route('plugins.deactivate', $plugin) }}" method="POST"
                                              x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            <button class="dropdown-item" type="submit">
                                                <i class="ti ti-player-pause me-2 text-muted"></i>
                                                <span x-show="!loading">Deactivate</span>
                                                <span x-show="loading" x-cloak>
                                                    <span class="spinner-border spinner-border-sm me-1"></span>Loading...
                                                </span>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('plugins.activate', $plugin) }}" method="POST"
                                              x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            <button class="dropdown-item" type="submit">
                                                <i class="ti ti-player-play me-2 text-muted"></i>
                                                <span x-show="!loading">Activate</span>
                                                <span x-show="loading" x-cloak>
                                                    <span class="spinner-border spinner-border-sm me-1"></span>Loading...
                                                </span>
                                            </button>
                                        </form>
                                        @endif

                                        @if($plugin->github_url && !$hasUpdate)
                                        <form action="{{ route('plugins.update', $plugin) }}" method="POST"
                                              x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            <button class="dropdown-item" type="submit">
                                                <i class="ti ti-refresh me-2 text-muted"></i>
                                                <span x-show="!loading">Check Update</span>
                                                <span x-show="loading" x-cloak>
                                                    <span class="spinner-border spinner-border-sm me-1"></span>Loading...
                                                </span>
                                            </button>
                                        </form>
                                        @endif

                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-uninstall-{{ $plugin->slug }}">
                                            <i class="ti ti-trash me-2"></i>Uninstall
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">
                        <i class="ti ti-puzzle mb-2" style="font-size:2rem;display:block"></i>
                        Belum ada plugin terinstall.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Marketplace --}}
<div class="card anim-fadein" style="animation-delay: 100ms">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-store me-2 text-muted"></i>Plugin Marketplace
        </h3>
    </div>
    <div class="card-body">
        @if(empty($registry))
        <div class="text-center text-muted py-5">
            <i class="ti ti-wifi-off mb-2" style="font-size:2rem;display:block"></i>
            <div>Tidak dapat terhubung ke registry.</div>
        </div>
        @else
        <div class="row g-3 anim-stagger">
            @foreach($registry as $item)
            @php $isInstalled = isset($installed[$item['slug']]); @endphp
            <div class="col-md-4">
                <div class="card card-sm {{ $isInstalled ? '' : 'card-hover' }} h-100">
                    <div class="card-body d-flex flex-column">
                        {{-- Header plugin --}}
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <div class="p-2 rounded bg-blue-lt text-blue" style="line-height:1">
                                <i class="{{ $item['icon'] ?? 'ti ti-puzzle' }}" style="font-size:1.25rem"></i>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="fw-medium">{{ $item['name'] }}</div>
                                <div class="text-muted small">{{ $item['category'] }}</div>
                            </div>
                            <span class="text-muted small">v{{ $item['version'] }}</span>
                        </div>

                        <p class="text-muted small flex-grow-1 mb-3">{{ $item['description'] }}</p>

                        {{-- Footer --}}
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted small">by {{ $item['author'] }}</span>

                            @if($isInstalled)
                            {{-- Sudah terinstall: teks halus, bukan badge mencolok --}}
                            <span class="text-success small d-flex align-items-center gap-1">
                                <i class="ti ti-circle-check"></i> Installed
                            </span>
                            @else
                            <form action="{{ route('plugins.install') }}" method="POST"
                                  x-data="{ loading: false }" @submit="loading = true">
                                @csrf
                                <input type="hidden" name="github_url" value="{{ $item['github_url'] }}">
                                <input type="hidden" name="download_url" value="{{ $item['download_url'] ?? '' }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <span x-show="!loading">
                                        <i class="ti ti-download me-1"></i>Install
                                    </span>
                                    <span x-show="loading" x-cloak>
                                        <span class="spinner-border spinner-border-sm me-1"></span>Installing...
                                    </span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Modals Uninstall --}}
@foreach($installed as $plugin)
@if(!$plugin->is_core)
<div class="modal modal-blur fade" id="modal-uninstall-{{ $plugin->slug }}"
    tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Uninstall {{ $plugin->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-muted small">
                Pilih opsi uninstall:
            </div>
            <div class="modal-footer d-block">
                <form action="{{ route('plugins.uninstall', $plugin) }}" method="POST" class="mb-2">
                    @csrf @method('DELETE')
                    <input type="hidden" name="remove_data" value="0">
                    <button type="submit" class="btn btn-outline-secondary w-100 text-start">
                        <i class="ti ti-database me-2"></i>
                        <span>
                            <div class="fw-medium">Keep Data</div>
                            <div class="text-muted small fw-normal">Tabel & data tetap tersimpan</div>
                        </span>
                    </button>
                </form>
                <form action="{{ route('plugins.uninstall', $plugin) }}" method="POST">
                    @csrf @method('DELETE')
                    <input type="hidden" name="remove_data" value="1">
                    <button type="submit" class="btn btn-outline-danger w-100 text-start"
                        onclick="return confirm('Yakin? Semua data {{ $plugin->name }} akan dihapus permanen!')">
                        <i class="ti ti-trash me-2"></i>
                        <span>
                            <div class="fw-medium">Remove Data</div>
                            <div class="small fw-normal opacity-75">Hapus tabel & semua data</div>
                        </span>
                    </button>
                </form>
                <button type="button" class="btn btn-ghost-secondary w-100 mt-2"
                    data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection

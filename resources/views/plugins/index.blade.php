@extends('layouts.app')

@section('title', 'Plugin Manager')
@section('page-title', 'Plugin Manager')

@section('content')

{{-- Installed Plugins --}}
<div class="card mb-4 anim-fadein">
    <div class="card-header">
        <h3 class="card-title">
            <i class="ti ti-puzzle me-2"></i>
            Installed Plugins ({{ $installed->count() }})
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Plugin</th>
                    <th>Version</th>
                    <th>Status</th>
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
                        <div class="fw-bold">{{ $plugin->name }}</div>
                        <div class="text-muted small">{{ $plugin->description }}</div>
                    </td>
                    <td>
                        <span class="badge bg-blue-lt">v{{ $plugin->version }}</span>
                        @if($hasUpdate)
                        <span class="badge bg-warning-lt ms-1">
                            <i class="ti ti-arrow-up me-1"></i>v{{ $latestVersion }} available
                        </span>
                        @endif
                    </td>
                    <td>
                        @if($plugin->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($plugin->is_core)
                                <span class="badge bg-purple-lt">
                                    <i class="ti ti-lock me-1"></i>Core
                                </span>
                            @else
                                {{-- Tombol Update --}}
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
                                    <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        @if($plugin->is_active)
                                        <form action="{{ route('plugins.deactivate', $plugin) }}" method="POST"
                                              x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            <button class="dropdown-item text-warning" type="submit">
                                                <span x-show="!loading">Deactivate</span>
                                                <span x-show="loading" x-cloak><span class="spinner-border spinner-border-sm me-1"></span>Loading...</span>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('plugins.activate', $plugin) }}" method="POST"
                                              x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            <button class="dropdown-item text-success" type="submit">
                                                <span x-show="!loading">Activate</span>
                                                <span x-show="loading" x-cloak><span class="spinner-border spinner-border-sm me-1"></span>Loading...</span>
                                            </button>
                                        </form>
                                        @endif
                                        @if($plugin->github_url && !$hasUpdate)
                                        <form action="{{ route('plugins.update', $plugin) }}" method="POST"
                                              x-data="{ loading: false }" @submit="loading = true">
                                            @csrf
                                            <button class="dropdown-item" type="submit">
                                                <span x-show="!loading">Check Update</span>
                                                <span x-show="loading" x-cloak><span class="spinner-border spinner-border-sm me-1"></span>Loading...</span>
                                            </button>
                                        </form>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-uninstall-{{ $plugin->slug }}">
                                            Uninstall
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
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
            <i class="ti ti-store me-2"></i>
            Plugin Marketplace
        </h3>
    </div>
    <div class="card-body">
        @if(empty($registry))
        <div class="text-center text-muted py-4">
            <i class="ti ti-wifi-off" style="font-size:2rem"></i>
            <div class="mt-2">Tidak dapat terhubung ke registry.</div>
        </div>
        @else
        <div class="row g-3 anim-stagger">
            @foreach($registry as $item)
            @php $isInstalled = isset($installed[$item['slug']]); @endphp
            <div class="col-md-4">
                <div class="card card-sm card-hover">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2" style="font-size:1.5rem">
                                <i class="{{ $item['icon'] ?? 'ti ti-puzzle' }}"></i>
                            </span>
                            <div>
                                <div class="fw-bold">{{ $item['name'] }}</div>
                                <div class="text-muted small">{{ $item['category'] }}</div>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-blue-lt">v{{ $item['version'] }}</span>
                            </div>
                        </div>
                        <p class="text-muted small mb-3">{{ $item['description'] }}</p>
                        <div class="d-flex align-items-center">
                            <span class="text-muted small me-auto">
                                by {{ $item['author'] }}
                            </span>
                            @if($isInstalled)
                            <span class="badge bg-success">
                                <i class="ti ti-check"></i> Installed
                            </span>
                            @else
                            <form action="{{ route('plugins.install') }}" method="POST"
                                  x-data="{ loading: false }" @submit="loading = true">
                                @csrf
                                <input type="hidden" name="github_url" value="{{ $item['github_url'] }}">
                                <input type="hidden" name="download_url" value="{{ $item['download_url'] ?? '' }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <span x-show="!loading">
                                        <i class="ti ti-download"></i> Install
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

{{-- Modals Uninstall — di luar table agar tidak jadi invalid HTML --}}
@foreach($installed as $plugin)
@if(!$plugin->is_core)
<div class="modal modal-blur fade" id="modal-uninstall-{{ $plugin->slug }}"
    tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Uninstall {{ $plugin->name }}</div>
                <div class="text-muted mt-1">Pilih opsi uninstall:</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <form action="{{ route('plugins.uninstall', $plugin) }}" method="POST" class="mb-2">
                        @csrf @method('DELETE')
                        <input type="hidden" name="remove_data" value="0">
                        <button type="submit" class="btn w-100 text-start">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-database me-2"></i>
                                <div>
                                    <div>Uninstall — Keep Data</div>
                                    <div class="text-muted small fw-normal">Tabel & data tetap tersimpan</div>
                                </div>
                            </div>
                        </button>
                    </form>
                    <form action="{{ route('plugins.uninstall', $plugin) }}" method="POST" class="mb-2">
                        @csrf @method('DELETE')
                        <input type="hidden" name="remove_data" value="1">
                        <button type="submit" class="btn btn-danger w-100 text-start"
                            onclick="return confirm('Yakin? Semua data {{ $plugin->name }} akan dihapus permanen!')">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-trash me-2"></i>
                                <div>
                                    <div>Uninstall — Remove Data</div>
                                    <div class="small fw-normal opacity-75">Hapus tabel & semua data</div>
                                </div>
                            </div>
                        </button>
                    </form>
                    <button type="button" class="btn btn-link w-100 text-muted"
                        data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection
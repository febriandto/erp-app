@extends('layouts.app')

@section('title', 'Plugin Manager')
@section('page-title', 'Plugin Manager')

@section('content')

{{-- Install dari GitHub --}}
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Install Plugin dari GitHub</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('plugins.install') }}" method="POST">
            @csrf
            <div class="row g-2">
                <div class="col">
                    <input type="url" name="github_url"
                           class="form-control @error('github_url') is-invalid @enderror"
                           placeholder="https://github.com/username/erp-plugin-hr"
                           value="{{ old('github_url') }}">
                    @error('github_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                            <path d="M7 11l5 5l5 -5"/>
                            <path d="M12 4l0 12"/>
                        </svg>
                        Install dari GitHub
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Daftar Plugin --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Installed Plugins ({{ $plugins->count() }})</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Plugin</th>
                    <th>Version</th>
                    <th>Author</th>
                    <th>Installed</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($plugins as $plugin)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $plugin->name }}</div>
                        @if($plugin->description)
                        <div class="text-muted small">{{ $plugin->description }}</div>
                        @endif
                        @if($plugin->github_url)
                        <div>
                            <a href="{{ $plugin->github_url }}" target="_blank"
                               class="text-muted small">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                     viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                                GitHub
                            </a>
                        </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-blue-lt">v{{ $plugin->version }}</span>
                    </td>
                    <td class="text-muted">{{ $plugin->author ?? '-' }}</td>
                    <td class="text-muted">{{ $plugin->installed_at?->format('d M Y') ?? '-' }}</td>
                    <td>
                        @if($plugin->is_active)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if($plugin->is_active)
                                <form action="{{ route('plugins.deactivate', $plugin) }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-warning" type="submit">
                                        Deactivate
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('plugins.activate', $plugin) }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-success" type="submit">
                                        Activate
                                    </button>
                                </form>
                                @endif

                                @if($plugin->github_url)
                                <form action="{{ route('plugins.update', $plugin) }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item" type="submit">
                                        Update (git pull)
                                    </button>
                                </form>
                                @endif

                                <div class="dropdown-divider"></div>

                                <form action="{{ route('plugins.uninstall', $plugin) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit"
                                        onclick="return confirm('Yakin uninstall plugin {{ $plugin->name }}? Folder plugin akan dihapus.')">
                                        Uninstall
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Belum ada plugin terinstall.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
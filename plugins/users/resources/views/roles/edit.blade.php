@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <form action="{{ route('users.roles.update', $role) }}" method="POST"
              x-data="{ loading: false }" @submit="loading = true">
            @csrf @method('PUT')

            <div class="card anim-fadein">
                <div class="card-header">
                    <h3 class="card-title">{{ $role->name }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label required">Nama Role</label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-medium">Permissions</label>
                        <div class="text-muted small mb-3">Centang permission yang boleh diakses role ini.</div>

                        @forelse($permissions as $module => $perms)
                        <div class="mb-3">
                            <div class="text-uppercase text-muted small fw-bold mb-2" style="letter-spacing:.05em">
                                {{ $module }}
                            </div>
                            <div class="row g-2">
                                @foreach($perms as $permission)
                                <div class="col-md-6">
                                    <label class="form-check">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->name }}"
                                               class="form-check-input"
                                               {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                        <span class="form-check-label">
                                            <span class="fw-medium">{{ $permission->name }}</span>
                                        </span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <div class="text-muted small">Belum ada permission. Aktifkan plugin terlebih dahulu.</div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Update</span>
                        <span x-show="loading" x-cloak>
                            <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
                        </span>
                    </button>
                    <a href="{{ route('users.roles.index') }}" class="btn">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

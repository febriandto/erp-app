@extends('layouts.app')

@section('title', 'Add User')
@section('page-title', 'Add User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card anim-fadein">
            <div class="card-header">
                <h3 class="card-title">Detail User</h3>
            </div>
            <form action="{{ route('users.store') }}" method="POST"
                  x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Nama lengkap" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="email@perusahaan.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-muted">(opsional)</span></label>
                        <input type="text" name="username" value="{{ old('username') }}"
                               class="form-control @error('username') is-invalid @enderror"
                               placeholder="contoh: johndoe (huruf, angka, _ dan -)">
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Password</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Min. 8 karakter" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <div class="row g-2">
                            @foreach($roles as $role)
                            <div class="col-auto">
                                <label class="form-check">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           class="form-check-input"
                                           {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                    <span class="form-check-label">{{ $role->name }}</span>
                                </label>
                            </div>
                            @endforeach
                            @if($roles->isEmpty())
                            <div class="col-12 text-muted small">Belum ada role. <a href="{{ route('users.roles.create') }}">Buat role</a> dulu.</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Simpan</span>
                        <span x-show="loading" x-cloak>
                            <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
                        </span>
                    </button>
                    <a href="{{ route('users.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Add Role')
@section('page-title', 'Add Role')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card anim-fadein">
            <div class="card-header">
                <h3 class="card-title">Role Baru</h3>
            </div>
            <form action="{{ route('users.roles.store') }}" method="POST"
                  x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Role</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="contoh: admin, manager, staff" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Simpan</span>
                        <span x-show="loading" x-cloak>
                            <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
                        </span>
                    </button>
                    <a href="{{ route('users.roles.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Add Tax')
@section('page-title', 'Add Tax')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card anim-fadein">
            <div class="card-header"><h3 class="card-title">Detail Tax</h3></div>
            <form action="{{ route('masterdata.taxes.store') }}" method="POST"
                  x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="PPN 11%" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Code <span class="text-muted small">(singkatan unik)</span></label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               class="form-control text-uppercase @error('code') is-invalid @enderror"
                               placeholder="PPN11" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Rate (%)</label>
                        <div class="input-group">
                            <input type="number" name="rate" value="{{ old('rate', 0) }}"
                                   step="0.01" min="0" max="100"
                                   class="form-control @error('rate') is-invalid @enderror" required>
                            <span class="input-group-text">%</span>
                        </div>
                        @error('rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="form-check-label">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Simpan</span>
                        <span x-show="loading" x-cloak><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
                    </button>
                    <a href="{{ route('masterdata.taxes.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

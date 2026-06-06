@extends('layouts.app')

@section('title', 'Edit Tax')
@section('page-title', 'Edit Tax')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card anim-fadein">
            <div class="card-header"><h3 class="card-title">{{ $tax->name }}</h3></div>
            <form action="{{ route('masterdata.taxes.update', $tax) }}" method="POST"
                  x-data="{ loading: false }" @submit="loading = true">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" value="{{ old('name', $tax->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Code</label>
                        <input type="text" name="code" value="{{ old('code', $tax->code) }}"
                               class="form-control text-uppercase @error('code') is-invalid @enderror" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Rate (%)</label>
                        <div class="input-group">
                            <input type="number" name="rate" value="{{ old('rate', $tax->rate) }}"
                                   step="0.01" min="0" max="100"
                                   class="form-control @error('rate') is-invalid @enderror" required>
                            <span class="input-group-text">%</span>
                        </div>
                        @error('rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $tax->is_active) ? 'checked' : '' }}>
                            <span class="form-check-label">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Update</span>
                        <span x-show="loading" x-cloak><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
                    </button>
                    <a href="{{ route('masterdata.taxes.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

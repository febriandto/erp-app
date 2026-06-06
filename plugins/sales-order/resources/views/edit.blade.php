@extends('layouts.app')

@section('title', 'Edit Sales Order')
@section('page-title', 'Edit Sales Order')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card anim-fadein">
            <div class="card-header">
                <h3 class="card-title">Edit Sales Order</h3>
            </div>
            <form action="{{ route('sales-order.update', $record) }}" method="POST"
                  x-data="{ loading: false }" @submit="loading = true">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name"
                               value="{{ old('name'" . (1 ? ", $record->name" : "") . ") }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- TODO: tambah field lain sesuai kebutuhan --}}
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Update</span>
                        <span x-show="loading" x-cloak>
                            <span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...
                        </span>
                    </button>
                    <a href="{{ route('sales-order.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
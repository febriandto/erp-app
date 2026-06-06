@extends('layouts.app')

@section('title', 'Edit Unit of Measure')
@section('page-title', 'Edit Unit of Measure')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card anim-fadein">
            <div class="card-header"><h3 class="card-title">{{ $uom->name }}</h3></div>
            <form action="{{ route('masterdata.uom.update', $uom) }}" method="POST"
                  x-data="{ loading: false }" @submit="loading = true">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" value="{{ old('name', $uom->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Symbol</label>
                        <input type="text" name="symbol" value="{{ old('symbol', $uom->symbol) }}"
                               class="form-control @error('symbol') is-invalid @enderror" required>
                        @error('symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Category</label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                            @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $uom->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Update</span>
                        <span x-show="loading" x-cloak><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
                    </button>
                    <a href="{{ route('masterdata.uom.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

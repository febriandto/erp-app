@extends('layouts.app')

@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('inventory.products.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">Product Name</label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Enter product name">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">SKU</label>
                        <input type="text" name="sku"
                               class="form-control @error('sku') is-invalid @enderror"
                               value="{{ old('sku') }}" placeholder="e.g. PRD-001">
                        @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Stock</label>
                            <input type="number" name="stock"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', 0) }}" min="0">
                            @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Price (Rp)</label>
                            <input type="number" name="price"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', 0) }}" min="0" step="100">
                            @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Optional description">{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex">
                        <a href="{{ route('inventory.products.index') }}" class="btn me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
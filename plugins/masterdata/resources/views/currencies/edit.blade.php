@extends('layouts.app')

@section('title', 'Edit Currency')
@section('page-title', 'Edit Currency')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card anim-fadein">
            <div class="card-header"><h3 class="card-title">{{ $currency->code }}</h3></div>
            <form action="{{ route('masterdata.currencies.update', $currency) }}" method="POST"
                  x-data="{ loading: false }" @submit="loading = true">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Code</label>
                        <input type="text" name="code" value="{{ old('code', $currency->code) }}" maxlength="3"
                               class="form-control text-uppercase @error('code') is-invalid @enderror" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Name</label>
                        <input type="text" name="name" value="{{ old('name', $currency->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Symbol</label>
                        <input type="text" name="symbol" value="{{ old('symbol', $currency->symbol) }}"
                               class="form-control @error('symbol') is-invalid @enderror" required>
                        @error('symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Exchange Rate</label>
                        <input type="number" name="exchange_rate" value="{{ old('exchange_rate', $currency->exchange_rate) }}"
                               step="0.000001" min="0.000001"
                               class="form-control @error('exchange_rate') is-invalid @enderror" required>
                        @error('exchange_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <span x-show="!loading"><i class="ti ti-check me-1"></i>Update</span>
                        <span x-show="loading" x-cloak><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
                    </button>
                    <a href="{{ route('masterdata.currencies.index') }}" class="btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

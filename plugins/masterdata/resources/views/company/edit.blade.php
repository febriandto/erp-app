@extends('layouts.app')

@section('title', 'Company Profile')
@section('page-title', 'Company Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <form action="{{ route('masterdata.company.update') }}" method="POST"
              x-data="{ loading: false }" @submit="loading = true">
            @csrf @method('PUT')

            <div class="card anim-fadein mb-3">
                <div class="card-header"><h3 class="card-title">Identitas Perusahaan</h3></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Nama Perusahaan</label>
                            <input type="text" name="name" value="{{ old('name', $company->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Legal</label>
                            <input type="text" name="legal_name" value="{{ old('legal_name', $company->legal_name) }}"
                                   class="form-control @error('legal_name') is-invalid @enderror"
                                   placeholder="PT. Nama Perusahaan">
                            @error('legal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NPWP / Tax Number</label>
                            <input type="text" name="tax_number" value="{{ old('tax_number', $company->tax_number) }}"
                                   class="form-control @error('tax_number') is-invalid @enderror"
                                   placeholder="00.000.000.0-000.000">
                            @error('tax_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card anim-fadein mb-3" style="animation-delay: 50ms">
                <div class="card-header"><h3 class="card-title">Alamat</h3></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" rows="2"
                                      class="form-control @error('address') is-invalid @enderror">{{ old('address', $company->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kota</label>
                            <input type="text" name="city" value="{{ old('city', $company->city) }}"
                                   class="form-control @error('city') is-invalid @enderror">
                            @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Negara</label>
                            <input type="text" name="country" value="{{ old('country', $company->country ?? 'Indonesia') }}"
                                   class="form-control @error('country') is-invalid @enderror">
                            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card anim-fadein mb-3" style="animation-delay: 100ms">
                <div class="card-header"><h3 class="card-title">Kontak</h3></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="+62 21 xxxx xxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $company->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="info@perusahaan.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" value="{{ old('website', $company->website) }}"
                                   class="form-control @error('website') is-invalid @enderror"
                                   placeholder="https://perusahaan.com">
                            @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <span x-show="!loading"><i class="ti ti-check me-1"></i>Simpan</span>
                    <span x-show="loading" x-cloak><span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

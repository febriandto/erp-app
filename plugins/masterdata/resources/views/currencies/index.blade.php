@extends('layouts.app')

@section('title', 'Currency')
@section('page-title', 'Currency')

@section('page-actions')
<a href="{{ route('masterdata.currencies.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-1"></i>Add Currency
</a>
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Symbol</th>
                    <th>Exchange Rate</th>
                    <th>Default</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($currencies as $currency)
                <tr>
                    <td><span class="badge bg-blue-lt fw-bold">{{ $currency->code }}</span></td>
                    <td>{{ $currency->name }}</td>
                    <td class="text-muted">{{ $currency->symbol }}</td>
                    <td class="text-muted">{{ number_format($currency->exchange_rate, 4) }}</td>
                    <td>
                        @if($currency->is_default)
                        <span class="badge bg-green-lt"><i class="ti ti-check me-1"></i>Default</span>
                        @else
                        <form action="{{ route('masterdata.currencies.set-default', $currency) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-ghost-secondary py-0">Set Default</button>
                        </form>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('masterdata.currencies.edit', $currency) }}" class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                                @if(!$currency->is_default)
                                <form action="{{ route('masterdata.currencies.destroy', $currency) }}" method="POST"
                                      onsubmit="return confirm('Hapus currency {{ $currency->code }}?')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger">
                                        <i class="ti ti-trash me-2"></i>Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada currency.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Tax')
@section('page-title', 'Tax')

@section('page-actions')
<a href="{{ route('masterdata.taxes.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-1"></i>Add Tax
</a>
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($taxes as $tax)
                <tr>
                    <td>{{ $tax->name }}</td>
                    <td><span class="badge bg-muted-lt">{{ $tax->code }}</span></td>
                    <td>{{ number_format($tax->rate, 2) }}%</td>
                    <td>
                        @if($tax->is_active)
                        <span class="badge bg-green-lt">Aktif</span>
                        @else
                        <span class="badge bg-red-lt">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('masterdata.taxes.edit', $tax) }}" class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                                <form action="{{ route('masterdata.taxes.destroy', $tax) }}" method="POST"
                                      onsubmit="return confirm('Hapus tax {{ $tax->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger">
                                        <i class="ti ti-trash me-2"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada tax.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

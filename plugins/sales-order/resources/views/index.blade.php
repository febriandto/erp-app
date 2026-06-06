@extends('layouts.app')

@section('title', 'Sales Order')
@section('page-title', 'Sales Order')

@section('page-actions')
@can('sales-order.manage')
<a href="{{ route('sales-order.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-1"></i>Add
</a>
@endcan
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Created</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($records as $record)
                <tr>
                    <td class="text-muted">{{ $record->id }}</td>
                    <td>{{ $record->name }}</td>
                    <td class="text-muted">{{ $record->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                @can('sales-order.manage')
                                <a href="{{ route('sales-order.edit', $record) }}" class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                                <form action="{{ route('sales-order.destroy', $record) }}" method="POST"
                                      onsubmit="return confirm('Hapus record ini?')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger">
                                        <i class="ti ti-trash me-2"></i>Hapus
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Belum ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
    <div class="card-footer d-flex justify-content-end">
        {{ $records->links() }}
    </div>
    @endif
</div>
@endsection
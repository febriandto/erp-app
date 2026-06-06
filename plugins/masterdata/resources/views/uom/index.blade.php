@extends('layouts.app')

@section('title', 'Unit of Measure')
@section('page-title', 'Unit of Measure')

@section('page-actions')
<a href="{{ route('masterdata.uom.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-1"></i>Add Unit
</a>
@endsection

@section('content')
<div class="card anim-fadein">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Symbol</th>
                    <th>Category</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody class="anim-stagger">
                @forelse($uoms as $uom)
                <tr>
                    <td>{{ $uom->name }}</td>
                    <td><code>{{ $uom->symbol }}</code></td>
                    <td>
                        <span class="badge bg-azure-lt">{{ \Plugins\masterdata\Models\Uom::$categories[$uom->category] ?? $uom->category }}</span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('masterdata.uom.edit', $uom) }}" class="dropdown-item">
                                    <i class="ti ti-edit me-2"></i>Edit
                                </a>
                                <form action="{{ route('masterdata.uom.destroy', $uom) }}" method="POST"
                                      onsubmit="return confirm('Hapus unit {{ $uom->name }}?')">
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
                    <td colspan="4" class="text-center text-muted py-4">Belum ada unit of measure.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')

@section('page-actions')
<a href="{{ route('inventory.products.create') }}" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2" class="icon">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M12 5l0 14"/>
        <path d="M5 12l14 0"/>
    </svg>
    Add Product
</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td class="text-muted">{{ $product->sku }}</td>
                    <td>
                        <span class="badge {{ $product->stock > 10 ? 'bg-success' : 'bg-danger' }} me-1"></span>
                        {{ $product->stock }}
                    </td>
                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('inventory.products.edit', $product) }}">
                                    Edit
                                </a>
                                <form action="{{ route('inventory.products.destroy', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit"
                                        onclick="return confirm('Yakin hapus product ini?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada product.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
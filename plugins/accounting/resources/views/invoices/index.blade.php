@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('page-actions')
<a href="{{ route('accounting.invoices.create') }}" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2" class="icon">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M12 5l0 14"/><path d="M5 12l14 0"/>
    </svg>
    Add Invoice
</a>
@endsection

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Client</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="w-1"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td><a href="{{ route('accounting.invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                    <td>{{ $invoice->client_name }}</td>
                    <td>{{ $invoice->issue_date->format('d M Y') }}</td>
                    <td>{{ $invoice->due_date->format('d M Y') }}</td>
                    <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $badge = match($invoice->status) {
                                'paid'    => 'bg-success',
                                'sent'    => 'bg-info',
                                'overdue' => 'bg-danger',
                                default   => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ ucfirst($invoice->status) }}</span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle align-text-top" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('accounting.invoices.show', $invoice) }}">
                                    View
                                </a>
                                <form action="{{ route('accounting.invoices.destroy', $invoice) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit"
                                        onclick="return confirm('Yakin hapus invoice ini?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada invoice.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
@endsection
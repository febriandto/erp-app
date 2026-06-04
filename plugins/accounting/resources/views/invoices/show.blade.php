@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', $invoice->invoice_number)

@section('page-actions')
<form action="{{ route('accounting.invoices.update-status', $invoice) }}" method="POST" class="d-flex gap-2">
    @csrf @method('PATCH')
    <select name="status" class="form-select w-auto">
        @foreach(['draft','sent','paid','overdue'] as $s)
        <option value="{{ $s }}" {{ $invoice->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn">Update Status</button>
</form>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-muted">Client</div>
                        <div class="fw-bold">{{ $invoice->client_name }}</div>
                        @if($invoice->client_email)
                        <div class="text-muted">{{ $invoice->client_email }}</div>
                        @endif
                    </div>
                    <div class="col-3">
                        <div class="text-muted">Issue Date</div>
                        <div>{{ $invoice->issue_date->format('d M Y') }}</div>
                    </div>
                    <div class="col-3">
                        <div class="text-muted">Due Date</div>
                        <div>{{ $invoice->due_date->format('d M Y') }}</div>
                    </div>
                </div>
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr>
                            <td>{{ $item->description }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
                @if($invoice->notes)
                <div class="mt-3 text-muted">{{ $invoice->notes }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
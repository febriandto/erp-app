@extends('layouts.app')

@section('title', 'Create Invoice')
@section('page-title', 'Create Invoice')

@section('content')
<form action="{{ route('accounting.invoices.store') }}" method="POST" id="invoice-form">
@csrf
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Invoice Details</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Invoice Number</label>
                        <input type="text" name="invoice_number"
                               class="form-control @error('invoice_number') is-invalid @enderror"
                               value="{{ old('invoice_number', $number) }}">
                        @error('invoice_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Client Name</label>
                        <input type="text" name="client_name"
                               class="form-control @error('client_name') is-invalid @enderror"
                               value="{{ old('client_name') }}">
                        @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Client Email</label>
                        <input type="email" name="client_email" class="form-control"
                               value="{{ old('client_email') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label required">Issue Date</label>
                        <input type="date" name="issue_date"
                               class="form-control @error('issue_date') is-invalid @enderror"
                               value="{{ old('issue_date', date('Y-m-d')) }}">
                        @error('issue_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label required">Due Date</label>
                        <input type="date" name="due_date"
                               class="form-control @error('due_date') is-invalid @enderror"
                               value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}">
                        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Items</h3></div>
            <div class="card-body p-0">
                <table class="table table-vcenter card-table" id="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="width:80px">Qty</th>
                            <th style="width:150px">Price</th>
                            <th style="width:150px">Subtotal</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <tr class="item-row">
                            <td><input type="text" name="items[0][description]" class="form-control" placeholder="Item description" required></td>
                            <td><input type="number" name="items[0][qty]" class="form-control qty" value="1" min="1" required></td>
                            <td><input type="number" name="items[0][price]" class="form-control price" value="0" min="0" step="100" required></td>
                            <td><span class="subtotal">0</span></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="px-3">
                                <button type="button" class="btn btn-sm" id="add-item">+ Add Item</button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold px-3">Total</td>
                            <td><strong id="grand-total">Rp 0</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Save Invoice</button>
                    <a href="{{ route('accounting.invoices.index') }}" class="btn">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<script>
let itemIndex = 1;

function formatRp(num) {
    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
}

function recalculate() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const subtotal = qty * price;
        row.querySelector('.subtotal').textContent = formatRp(subtotal);
        total += subtotal;
    });
    document.getElementById('grand-total').textContent = formatRp(total);
}

document.getElementById('items-body').addEventListener('input', recalculate);

document.getElementById('add-item').addEventListener('click', function() {
    const tbody = document.getElementById('items-body');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td><input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="Item description" required></td>
        <td><input type="number" name="items[${itemIndex}][qty]" class="form-control qty" value="1" min="1" required></td>
        <td><input type="number" name="items[${itemIndex}][price]" class="form-control price" value="0" min="0" step="100" required></td>
        <td><span class="subtotal">Rp 0</span></td>
        <td><button type="button" class="btn btn-sm btn-danger remove-item">×</button></td>
    `;
    tbody.appendChild(row);
    itemIndex++;
});

document.getElementById('items-body').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        e.target.closest('tr').remove();
        recalculate();
    }
});
</script>
@endsection
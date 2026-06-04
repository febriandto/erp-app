<?php

namespace Plugins\accounting\Controllers;

use App\Http\Controllers\Controller;
use Plugins\accounting\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->paginate(20);
        return view('accounting::invoices.index', compact('invoices'));
    }

    public function create()
    {
        $number = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);
        return view('accounting::invoices.create', compact('number'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|unique:invoices,invoice_number',
            'client_name'    => 'required|string|max:255',
            'issue_date'     => 'required|date',
            'due_date'       => 'required|date|after_or_equal:issue_date',
            'items'          => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.price'  => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create($request->except('items'));
        $invoice->items()->createMany($request->items);

        return redirect()->route('accounting.invoices.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items');
        return view('accounting::invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('accounting.invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue',
        ]);
        $invoice->update(['status' => $request->status]);
        return back()->with('success', 'Status invoice diupdate.');
    }
}
<?php

namespace Plugins\SalesOrder\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugins\SalesOrder\Models\SalesOrder;

class SalesOrderController extends Controller
{
    public function index()
    {
        $records = SalesOrder::latest()->paginate(20);
        return view('sales-order::index', compact('records'));
    }

    public function create()
    {
        return view('sales-order::create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        SalesOrder::create($request->only('name'));

        return redirect()->route('sales-order.index')
            ->with('success', 'Record berhasil dibuat.');
    }

    public function show(SalesOrder $record)
    {
        return view('sales-order::show', compact('record'));
    }

    public function edit(SalesOrder $record)
    {
        return view('sales-order::edit', compact('record'));
    }

    public function update(Request $request, SalesOrder $record)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $record->update($request->only('name'));

        return redirect()->route('sales-order.index')
            ->with('success', 'Record berhasil diupdate.');
    }

    public function destroy(SalesOrder $record)
    {
        $record->delete();
        return redirect()->route('sales-order.index')
            ->with('success', 'Record berhasil dihapus.');
    }
}
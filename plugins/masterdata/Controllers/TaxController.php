<?php

namespace Plugins\masterdata\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugins\masterdata\Models\Tax;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::orderBy('name')->get();
        return view('masterdata::taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('masterdata::taxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:taxes',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        Tax::create([
            'name'      => $request->name,
            'code'      => strtoupper($request->code),
            'rate'      => $request->rate,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('masterdata.taxes.index')
            ->with('success', "Tax '{$request->name}' berhasil ditambahkan.");
    }

    public function edit(Tax $tax)
    {
        return view('masterdata::taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:taxes,code,' . $tax->id,
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        $tax->update([
            'name'      => $request->name,
            'code'      => strtoupper($request->code),
            'rate'      => $request->rate,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('masterdata.taxes.index')
            ->with('success', "Tax '{$tax->name}' berhasil diupdate.");
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();
        return redirect()->route('masterdata.taxes.index')
            ->with('success', "Tax '{$tax->name}' berhasil dihapus.");
    }
}

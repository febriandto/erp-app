<?php

namespace Plugins\masterdata\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugins\masterdata\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderByDesc('is_default')->orderBy('code')->get();
        return view('masterdata::currencies.index', compact('currencies'));
    }

    public function create()
    {
        return view('masterdata::currencies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'          => 'required|string|size:3|unique:currencies|uppercase',
            'name'          => 'required|string|max:100',
            'symbol'        => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
        ]);

        Currency::create([
            'code'          => strtoupper($request->code),
            'name'          => $request->name,
            'symbol'        => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
            'is_default'    => false,
        ]);

        return redirect()->route('masterdata.currencies.index')
            ->with('success', "Currency {$request->code} berhasil ditambahkan.");
    }

    public function edit(Currency $currency)
    {
        return view('masterdata::currencies.edit', compact('currency'));
    }

    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'code'          => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'name'          => 'required|string|max:100',
            'symbol'        => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
        ]);

        $currency->update([
            'code'          => strtoupper($request->code),
            'name'          => $request->name,
            'symbol'        => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
        ]);

        return redirect()->route('masterdata.currencies.index')
            ->with('success', "Currency {$currency->code} berhasil diupdate.");
    }

    public function destroy(Currency $currency)
    {
        if ($currency->is_default) {
            return back()->with('error', 'Currency default tidak bisa dihapus.');
        }

        $currency->delete();
        return redirect()->route('masterdata.currencies.index')
            ->with('success', "Currency {$currency->code} berhasil dihapus.");
    }

    public function setDefault(Currency $currency)
    {
        Currency::where('is_default', true)->update(['is_default' => false]);
        $currency->update(['is_default' => true]);

        return back()->with('success', "{$currency->code} dijadikan currency default.");
    }
}

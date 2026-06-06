<?php

namespace Plugins\masterdata\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugins\masterdata\Models\Uom;

class UomController extends Controller
{
    public function index()
    {
        $uoms = Uom::orderBy('category')->orderBy('name')->get();
        return view('masterdata::uom.index', compact('uoms'));
    }

    public function create()
    {
        $categories = Uom::$categories;
        return view('masterdata::uom.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100|unique:uoms',
            'symbol'   => 'required|string|max:20',
            'category' => 'required|in:' . implode(',', array_keys(Uom::$categories)),
        ]);

        Uom::create($request->only('name', 'symbol', 'category'));

        return redirect()->route('masterdata.uom.index')
            ->with('success', "Unit of Measure '{$request->name}' berhasil ditambahkan.");
    }

    public function edit(Uom $uom)
    {
        $categories = Uom::$categories;
        return view('masterdata::uom.edit', compact('uom', 'categories'));
    }

    public function update(Request $request, Uom $uom)
    {
        $request->validate([
            'name'     => 'required|string|max:100|unique:uoms,name,' . $uom->id,
            'symbol'   => 'required|string|max:20',
            'category' => 'required|in:' . implode(',', array_keys(Uom::$categories)),
        ]);

        $uom->update($request->only('name', 'symbol', 'category'));

        return redirect()->route('masterdata.uom.index')
            ->with('success', "Unit '{$uom->name}' berhasil diupdate.");
    }

    public function destroy(Uom $uom)
    {
        $uom->delete();
        return redirect()->route('masterdata.uom.index')
            ->with('success', "Unit '{$uom->name}' berhasil dihapus.");
    }
}

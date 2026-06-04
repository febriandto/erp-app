<?php

namespace Plugins\inventory\Controllers;

use App\Http\Controllers\Controller;
use Plugins\inventory\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('inventory::products.index', compact('products'));
    }

    public function create()
    {
        return view('inventory::products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'sku'   => 'required|string|unique:products,sku',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($request->all());

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('inventory::products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'sku'   => 'required|string|unique:products,sku,' . $product->id,
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('inventory.products.index')
            ->with('success', 'Product berhasil dihapus.');
    }
}
<?php

namespace Plugins\masterdata\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Plugins\masterdata\Models\CompanyProfile;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = CompanyProfile::instance();
        return view('masterdata::company.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:500',
            'city'       => 'nullable|string|max:100',
            'country'    => 'nullable|string|max:100',
            'phone'      => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:255',
            'website'    => 'nullable|url|max:255',
        ]);

        CompanyProfile::instance()->update($request->only(
            'name', 'legal_name', 'tax_number',
            'address', 'city', 'country',
            'phone', 'email', 'website'
        ));

        return back()->with('success', 'Company profile berhasil disimpan.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CompanySettingController extends Controller
{
    /**
     * Display the company settings form.
     */
    public function edit()
    {
        $setting = CompanySetting::first();

        return view('settings.company', compact('setting'));
    }

    /**
     * Update the company settings data.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'currency_symbol' => ['nullable', 'string', 'max:10'],
            'bill_prefix' => ['nullable', 'string', 'max:10'],
            'challan_prefix' => ['nullable', 'string', 'max:10'],
            'pv_prefix' => ['nullable', 'string', 'max:10'],
            'rv_prefix' => ['nullable', 'string', 'max:10'],
            'jv_prefix' => ['nullable', 'string', 'max:10'],
            'other_details' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $setting = CompanySetting::first();
        $data = $validated;
        unset($data['logo']);

        if ($request->hasFile('logo')) {
            // Delete old logo if it exists
            if ($setting && $setting->logo_path && \Storage::disk('public')->exists($setting->logo_path)) {
                \Storage::disk('public')->delete($setting->logo_path);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $path;
        }

        if ($setting) {
            $setting->update($data);
        } else {
            $setting = CompanySetting::create($data);
        }

        Cache::forget('company_setting');
        Cache::forever('company_setting', $setting);

        return redirect()->back()->with('success', 'Company settings updated successfully.');
    }
}

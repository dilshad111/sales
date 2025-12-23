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
        ]);

        $setting = CompanySetting::first();

        if ($setting) {
            $setting->update($validated);
        } else {
            $setting = CompanySetting::create($validated);
        }

        Cache::forget('company_setting');
        Cache::forever('company_setting', $setting);

        return redirect()->route('settings.company.edit')->with('success', 'Company settings updated successfully.');
    }
}

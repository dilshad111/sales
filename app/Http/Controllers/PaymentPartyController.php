<?php

namespace App\Http\Controllers;

use App\Models\PaymentParty;
use Illuminate\Http\Request;

class PaymentPartyController extends Controller
{
    public function index()
    {
        $parties = PaymentParty::all();
        return view('payment_parties.index', compact('parties'));
    }

    public function create()
    {
        return view('payment_parties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'opening_balance' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        PaymentParty::create($validated);

        return redirect()->route('payment_parties.index')->with('success', 'Payment Party created successfully.');
    }

    public function edit(PaymentParty $payment_party)
    {
        return view('payment_parties.edit', compact('payment_party'));
    }

    public function update(Request $request, PaymentParty $payment_party)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'opening_balance' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $payment_party->update($validated);

        return redirect()->route('payment_parties.index')->with('success', 'Payment Party updated successfully.');
    }

    public function destroy(PaymentParty $payment_party)
    {
        $payment_party->delete();
        return redirect()->route('payment_parties.index')->with('success', 'Payment Party deleted successfully.');
    }
}

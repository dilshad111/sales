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
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        PaymentParty::create($request->all());

        return redirect()->route('payment_parties.index')->with('success', 'Payment Party created successfully.');
    }

    public function edit(PaymentParty $payment_party)
    {
        return view('payment_parties.edit', compact('payment_party'));
    }

    public function update(Request $request, PaymentParty $payment_party)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $payment_party->update($request->all());

        return redirect()->route('payment_parties.index')->with('success', 'Payment Party updated successfully.');
    }

    public function destroy(PaymentParty $payment_party)
    {
        $payment_party->delete();
        return redirect()->route('payment_parties.index')->with('success', 'Payment Party deleted successfully.');
    }
}

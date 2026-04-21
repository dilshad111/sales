<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $roots = Account::root()->with('children.children')->orderBy('code')->get();
        return view('accounts.index', compact('roots'));
    }

    public function create(Request $request)
    {
        $selectedParentId = $request->input('parent_id');
        $suggestedCode = '';
        
        if ($selectedParentId) {
            $parent = Account::find($selectedParentId);
            if ($parent && is_numeric($parent->code)) {
                $parentCode = (string)$parent->code;
                $lastChild = $parent->children()->orderBy('code', 'desc')->first();
                
                if ($lastChild && is_numeric($lastChild->code)) {
                    $lastCode = (int)$lastChild->code;
                    // Detect step based on trailing zeros of the last code
                    // e.g., 1100 -> step 100; 1110 -> step 10; 1111 -> step 1
                    if ($lastCode % 100 === 0) { $step = 100; }
                    elseif ($lastCode % 10 === 0) { $step = 10; }
                    else { $step = 1; }
                    
                    $suggestedCode = $lastCode + $step;
                } else {
                    // First child logic: Determine step based on parent's trailing zeros
                    // 1000 -> 1100 (step 100)
                    // 1100 -> 1110 (step 10)
                    // 1110 -> 1111 (step 1)
                    if ((int)$parentCode % 1000 === 0) { $step = 100; }
                    elseif ((int)$parentCode % 100 === 0) { $step = 10; }
                    else { $step = 1; }
                    
                    $suggestedCode = (int)$parentCode + $step;
                }

                // Avoid duplication: if suggested code exists, increment until unique
                while (Account::where('code', $suggestedCode)->exists()) {
                    // Use a smaller step if the current step is blocked? 
                    // No, usually just move to the next in the same series.
                    $suggestedCode += (isset($step) ? $step : 1);
                }
            }
        }

        $parents = Account::where('is_group', true)->orderBy('name')->get();
        return view('accounts.create', compact('parents', 'selectedParentId', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:accounts,code',
            'parent_id' => 'nullable|exists:accounts,id',
            'is_group' => 'required|boolean',
            'type' => 'required_without:parent_id|string|in:Asset,Liability,Equity,Income,Expense',
            'opening_balance' => 'required|numeric',
        ]);

        $parent = null;
        if ($request->parent_id) {
            $parent = Account::find($request->parent_id);
            if (!$parent->is_group) {
                return back()->with('error', 'Parent must be a group account.')->withInput();
            }
        }

        Account::create([
            'name' => $request->name,
            'code' => $request->code,
            'parent_id' => $request->parent_id,
            'is_group' => $request->is_group,
            'type' => $parent ? $parent->type : $request->type, // Inherit type if parent exists
            'category' => 'general',
            'opening_balance' => $request->opening_balance,
            'created_by' => Auth::id(),
            'status' => 'active'
        ]);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        $parents = Account::where('is_group', true)->where('id', '!=', $account->id)->orderBy('name')->get();
        return view('accounts.edit', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:accounts,code,' . $account->id,
            'status' => 'required|in:active,inactive',
            'opening_balance' => 'required|numeric',
        ]);

        $account->update([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
            'opening_balance' => $request->opening_balance,
            'updated_by' => Auth::id()
        ]);

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        if ($account->category === 'customer') {
            return back()->with('error', 'Customer accounts cannot be deleted directly. Please delete the customer instead.');
        }

        if ($account->children()->exists()) {
            return back()->with('error', 'Account cannot be deleted because it has sub-accounts.');
        }

        if ($account->entries()->exists()) {
            return back()->with('error', 'Account cannot be deleted because it has transaction history.');
        }

        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}

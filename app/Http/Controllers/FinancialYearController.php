<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use App\Services\FinancialYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialYearController extends Controller
{
    protected $fyService;

    public function __construct(FinancialYearService $fyService)
    {
        $this->fyService = $fyService;
    }

    public function index()
    {
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        return view('financial_years.index', compact('financialYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        FinancialYear::create($request->all());

        return redirect()->route('financial_years.index')
            ->with('success', 'Financial Year created successfully.');
    }

    public function close(FinancialYear $financialYear)
    {
        try {
            $this->fyService->closeYear($financialYear, Auth::id());
            return redirect()->route('financial_years.index')
                ->with('success', 'Financial Year ' . $financialYear->name . ' closed successfully. Opening balances generated.');
        } catch (\Exception $e) {
            return redirect()->route('financial_years.index')
                ->with('error', 'Error closing year: ' . $e->getMessage());
        }
    }

    /**
     * Bonus: Reopen financial year (Admin only)
     */
    public function reopen(FinancialYear $financialYear)
    {
        if (Auth::user()->role !== 'Admin') {
             return redirect()->route('financial_years.index')->with('error', 'Unauthorized. Only Admins can reopen a financial year.');
        }

        $financialYear->update([
            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null
        ]);

        return redirect()->route('financial_years.index')
            ->with('success', 'Financial Year reopened successfully.');
    }
}

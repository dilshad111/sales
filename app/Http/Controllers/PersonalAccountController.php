<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\CommissionPayment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonalAccountController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request->input('from_date'), $request->input('to_date'));

        $usersQuery = User::query()
            ->with(['commissions']) // Still keeping for UI info if needed
            ->orderBy('name');

        if ($search = $request->input('search')) {
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }

        $users = $usersQuery->paginate(20);

        // Fetch Accounting Balances for each user
        $users->getCollection()->transform(function ($user) use ($from, $to) {
            $account = \App\Models\Account::where('user_id', $user->id)->first();
            
            if ($account) {
                $user->account_id = $account->id;
                
                // Fetch transaction sums within date range
                $query = $account->entries();
                if ($from) $query->whereHas('transaction', fn($q) => $q->where('date', '>=', $from->toDateString()));
                if ($to) $query->whereHas('transaction', fn($q) => $q->where('date', '<=', $to->toDateString()));
                
                $sums = $query->selectRaw('SUM(debit) as total_debits, SUM(credit) as total_credits')->first();
                
                // Note: For liability accounts (what we owe them), Credit is increase (commission), Debit is decrease (payment)
                // Outstanding for them is Credits - Debits
                $user->total_commission = (float) $sums->total_credits;
                $user->total_payments = (float) $sums->total_debits;
                $user->account_balance = $user->total_commission - $user->total_payments;
            } else {
                $user->total_commission = 0;
                $user->total_payments = 0;
                $user->account_balance = 0;
            }

            return $user;
        });

        $usersForForms = User::orderBy('name')->get();

        return view('personal_accounts.index', [
            'users' => $users,
            'usersForForms' => $usersForForms,
            'filters' => [
                'search' => $search,
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
            ],
        ]);
    }

    public function storeCommission(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'commission_date' => 'required|date|before_or_equal:today',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Commission::create($data);

        return $this->redirectAfterAction($request)->with('success', 'Commission recorded successfully.');
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'commission_id' => 'nullable|exists:commissions,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        CommissionPayment::create($data);

        return $this->redirectAfterAction($request)->with('success', 'Payment recorded successfully.');
    }

    public function show(Request $request, User $user)
    {
        [$from, $to] = $this->parseDateRange($request->input('from_date'), $request->input('to_date'));

        $statement = $this->buildStatement($user, $from, $to);

        $openCommissions = $user->commissions()
            ->withSum('payments as payments_total', 'amount')
            ->orderByDesc('commission_date')
            ->get()
            ->map(function (Commission $commission) {
                $paid = (float) ($commission->payments_total ?? 0);
                $commission->outstanding = (float) max($commission->amount - $paid, 0);
                return $commission;
            })
            ->filter(function (Commission $commission) {
                return $commission->outstanding > 0;
            });

        $usersForForms = User::orderBy('name')->get();

        return view('personal_accounts.statement', [
            'user' => $user,
            'statement' => $statement,
            'filters' => [
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
            ],
            'usersForForms' => $usersForForms,
            'openCommissions' => $openCommissions,
        ]);
    }

    public function statementPdf(Request $request, User $user)
    {
        [$from, $to] = $this->parseDateRange($request->input('from_date'), $request->input('to_date'));

        $statement = $this->buildStatement($user, $from, $to);

        $pdf = Pdf::loadView('personal_accounts.statement_pdf', [
            'user' => $user,
            'statement' => $statement,
            'filters' => [
                'from_date' => $request->input('from_date'),
                'to_date' => $request->input('to_date'),
            ],
        ]);

        $filename = 'statement_' . str_replace(' ', '_', strtolower($user->name)) . '.pdf';

        return $pdf->download($filename);
    }

    public function statementCsv(Request $request, User $user): StreamedResponse
    {
        [$from, $to] = $this->parseDateRange($request->input('from_date'), $request->input('to_date'));

        $statement = $this->buildStatement($user, $from, $to);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="statement_' . str_replace(' ', '_', strtolower($user->name)) . '.csv"',
        ];

        $columns = ['Date', 'Type', 'Reference', 'Notes', 'Commission Amount', 'Payment Amount', 'Balance'];

        $callback = function () use ($statement, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($statement['entries'] as $entry) {
                fputcsv($handle, [
                    $entry['display_date'],
                    ucfirst($entry['type']),
                    $entry['reference'],
                    $entry['notes'],
                    number_format($entry['commission_amount'], 2),
                    number_format($entry['payment_amount'], 2),
                    number_format($entry['balance'], 2),
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, null, $headers);
    }

    protected function buildStatement(User $user, ?Carbon $from, ?Carbon $to): array
    {
        $account = \App\Models\Account::where('user_id', $user->id)->first();
        
        if (!$account) {
            return [
                'entries' => collect(),
                'commission_total' => 0,
                'payment_total' => 0,
                'balance' => 0,
            ];
        }

        $query = $account->entries()->with('transaction');
        
        if ($from) $query->whereHas('transaction', fn($q) => $q->where('date', '>=', $from->toDateString()));
        if ($to) $query->whereHas('transaction', fn($q) => $q->where('date', '<=', $to->toDateString()));
        
        $entriesData = $query->join('transactions', 'transaction_entries.transaction_id', '=', 'transactions.id')
            ->orderBy('transactions.date', 'asc')
            ->orderBy('transactions.created_at', 'asc')
            ->select('transaction_entries.*', 'transactions.date as tx_date', 'transactions.narration', 'transactions.type as tx_type')
            ->get();

        $entries = collect();
        $runningBalance = 0.0;

        foreach ($entriesData as $entry) {
            // Note: For liability accounts (what we owe them), Credit is increase (commission), Debit is decrease (payment)
            $runningBalance += ($entry->credit - $entry->debit);
            
            $entries->push([
                'id' => $entry->id,
                'type' => $entry->credit > 0 ? 'commission' : 'payment',
                'date' => Carbon::parse($entry->tx_date),
                'display_date' => Carbon::parse($entry->tx_date)->format('d/m/Y'),
                'reference' => $entry->tx_type,
                'notes' => $entry->narration,
                'commission_amount' => (float) $entry->credit,
                'payment_amount' => (float) $entry->debit,
                'amount' => $entry->credit > 0 ? (float)$entry->credit : (float)$entry->debit,
                'running_balance' => round($runningBalance, 2),
                'balance' => round($runningBalance, 2),
                'commission_ref' => null, // Not used in ledger view
            ]);
        }

        return [
            'entries' => $entries,
            'commission_total' => round($entriesData->sum('credit'), 2),
            'payment_total' => round($entriesData->sum('debit'), 2),
            'balance' => round($runningBalance, 2),
        ];
    }

    protected function parseDateRange(?string $from, ?string $to): array
    {
        $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
        $toDate = $to ? Carbon::parse($to)->endOfDay() : null;

        return [$fromDate, $toDate];
    }

    protected function redirectAfterAction(Request $request)
    {
        $redirectTo = $request->input('redirect_to');

        if ($redirectTo) {
            return redirect($redirectTo);
        }

        return redirect()->route('personal_accounts.index');
    }
}

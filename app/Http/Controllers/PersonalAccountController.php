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
            ->where(function ($query) {
                $query->whereHas('commissions')
                    ->orWhereHas('commissionPayments');
            });

        if ($search = $request->input('search')) {
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }

        $usersQuery
            ->withSum(['commissions as commission_sum' => function ($query) use ($from, $to) {
                if ($from) {
                    $query->whereDate('commission_date', '>=', $from->toDateString());
                }
                if ($to) {
                    $query->whereDate('commission_date', '<=', $to->toDateString());
                }
            }], 'amount')
            ->withSum(['commissionPayments as payment_sum' => function ($query) use ($from, $to) {
                if ($from) {
                    $query->whereDate('payment_date', '>=', $from->toDateString());
                }
                if ($to) {
                    $query->whereDate('payment_date', '<=', $to->toDateString());
                }
            }], 'amount');

        $users = $usersQuery
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $usersForForms = User::orderBy('name')->get();

        $openCommissions = Commission::with('user')
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
            })
            ->groupBy('user_id');

        return view('personal_accounts.index', [
            'users' => $users,
            'usersForForms' => $usersForForms,
            'openCommissions' => $openCommissions,
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
            'commission_date' => 'required|date',
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
            'payment_date' => 'required|date',
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
        $commissionsQuery = $user->commissions()->orderBy('commission_date')->orderBy('created_at');
        if ($from) {
            $commissionsQuery->whereDate('commission_date', '>=', $from->toDateString());
        }
        if ($to) {
            $commissionsQuery->whereDate('commission_date', '<=', $to->toDateString());
        }
        $commissions = $commissionsQuery->get();

        $paymentsQuery = $user->commissionPayments()->orderBy('payment_date')->orderBy('created_at');
        if ($from) {
            $paymentsQuery->whereDate('payment_date', '>=', $from->toDateString());
        }
        if ($to) {
            $paymentsQuery->whereDate('payment_date', '<=', $to->toDateString());
        }
        $payments = $paymentsQuery->get();

        $entries = collect();

        foreach ($commissions as $commission) {
            $date = $commission->commission_date ?? $commission->created_at;
            $entries->push([
                'type' => 'commission',
                'date' => $date instanceof Carbon ? $date : Carbon::parse($date),
                'reference' => $commission->reference ?: '-',
                'notes' => $commission->notes ?: '-',
                'commission_amount' => (float) $commission->amount,
                'payment_amount' => 0.0,
            ]);
        }

        foreach ($payments as $payment) {
            $date = $payment->payment_date ?? $payment->created_at;
            $entries->push([
                'type' => 'payment',
                'date' => $date instanceof Carbon ? $date : Carbon::parse($date),
                'reference' => $payment->reference ?: '-',
                'notes' => $payment->notes ?: '-',
                'commission_amount' => 0.0,
                'payment_amount' => (float) $payment->amount,
            ]);
        }

        $entries = $entries
            ->sortBy('date')
            ->values()
            ->map(function (array $entry) {
                $entry['display_date'] = $entry['date'] ? $entry['date']->format('d/m/Y') : '-';
                return $entry;
            });

        $runningBalance = 0.0;
        $entries = $entries->map(function (array $entry) use (&$runningBalance) {
            if ($entry['type'] === 'commission') {
                $runningBalance += $entry['commission_amount'];
            } else {
                $runningBalance -= $entry['payment_amount'];
            }
            $entry['balance'] = round($runningBalance, 2);
            return $entry;
        });

        return [
            'entries' => $entries,
            'commission_total' => round($commissions->sum('amount'), 2),
            'payment_total' => round($payments->sum('amount'), 2),
            'balance' => round($entries->last()['balance'] ?? 0, 2),
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

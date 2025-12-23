<?php

namespace App\Http\Controllers;

use App\Models\CartonCosting;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CartonCostingController extends Controller
{
    private const PLY_ALLOWANCES = [
        3 => 12,
        5 => 20,
        7 => 25,
    ];

    private const FLUTE_FACTORS = [
        'B' => 1.35,
        'C' => 1.45,
    ];

    private const DEFAULT_WASTAGE_RATE = 0.11;
    private const DEFAULT_OVERHEAD_RATE = 0.30;
    private const DEFAULT_PROFIT_RATE = 0.10;

    public function index(): View
    {
        $ply = 3;
        $layerTemplates = $this->getLayerTemplates();

        return view('carton_costing.index', [
            'fefcoCodes' => $this->getFefcoCodes(),
            'layerTemplates' => $layerTemplates,
            'formData' => $this->getDefaultFormData($ply, $layerTemplates),
            'fluteFactors' => self::FLUTE_FACTORS,
            'result' => null,
            'customers' => Customer::orderBy('name')->get(),
            'editMode' => false,
            'cartonCosting' => null,
        ]);
    }

    public function calculate(Request $request): View
    {
        $layerTemplates = $this->getLayerTemplates();
        $ply = (int) $request->input('ply', 3);

        if (! array_key_exists($ply, $layerTemplates)) {
            $ply = 3;
        }

        $rules = [
            'carton_costing_id' => ['nullable', 'integer', Rule::exists('carton_costings', 'id')],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'fefco_code' => ['required', 'string', Rule::in(array_keys($this->getFefcoCodes()))],
            'ply' => ['required', Rule::in(array_keys($layerTemplates))],
            'length' => ['required', 'numeric', 'gt:0'],
            'width' => ['required', 'numeric', 'gt:0'],
            'height' => ['required', 'numeric', 'gt:0'],
            'wastage_rate' => ['required', 'numeric', 'gte:0'],
            'overhead_rate' => ['required', 'numeric', 'gte:0'],
            'profit_rate' => ['required', 'numeric', 'gte:0'],
            'layers' => ['required', 'array', 'size:' . count($layerTemplates[$ply])],
            'layers.*.quality' => ['required', 'string', 'max:255'],
            'layers.*.gsm' => ['required', 'numeric', 'gte:0'],
            'layers.*.rate' => ['required', 'numeric', 'gte:0'],
        ];

        foreach ($layerTemplates[$ply] as $index => $template) {
            $rules["layers.$index.flute"] = $template['is_flute']
                ? ['required', Rule::in(array_keys(self::FLUTE_FACTORS))]
                : ['nullable'];
        }

        $validated = $request->validate($rules);
        $cartonCostingId = $validated['carton_costing_id'] ?? null;
        unset($validated['carton_costing_id']);

        $validated['layers'] = array_values($validated['layers']);
        $validated['ply'] = $ply;

        $calculation = $this->performCalculation($validated, $layerTemplates[$ply]);

        $formData = $validated;
        $formData['carton_costing_id'] = $cartonCostingId;

        return view('carton_costing.index', [
            'fefcoCodes' => $this->getFefcoCodes(),
            'layerTemplates' => $layerTemplates,
            'formData' => $formData,
            'fluteFactors' => self::FLUTE_FACTORS,
            'result' => $calculation,
            'customers' => Customer::orderBy('name')->get(),
            'editMode' => (bool) $cartonCostingId,
            'cartonCosting' => $cartonCostingId ? CartonCosting::find($cartonCostingId) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $layerTemplates = $this->getLayerTemplates();
        $ply = (int) $request->input('ply', 3);

        if (! array_key_exists($ply, $layerTemplates)) {
            $ply = 3;
        }

        $rules = [
            'carton_costing_id' => ['nullable', 'integer', Rule::exists('carton_costings', 'id')],
            'customer_id' => ['required', 'exists:customers,id'],
            'fefco_code' => ['required', 'string', Rule::in(array_keys($this->getFefcoCodes()))],
            'ply' => ['required', Rule::in(array_keys($layerTemplates))],
            'length' => ['required', 'numeric', 'gt:0'],
            'width' => ['required', 'numeric', 'gt:0'],
            'height' => ['required', 'numeric', 'gt:0'],
            'wastage_rate' => ['required', 'numeric', 'gte:0'],
            'overhead_rate' => ['required', 'numeric', 'gte:0'],
            'profit_rate' => ['required', 'numeric', 'gte:0'],
            'layers' => ['required', 'array', 'size:' . count($layerTemplates[$ply])],
            'layers.*.quality' => ['required', 'string', 'max:255'],
            'layers.*.gsm' => ['required', 'numeric', 'gte:0'],
            'layers.*.rate' => ['required', 'numeric', 'gte:0'],
        ];

        foreach ($layerTemplates[$ply] as $index => $template) {
            $rules["layers.$index.flute"] = $template['is_flute']
                ? ['required', Rule::in(array_keys(self::FLUTE_FACTORS))]
                : ['nullable'];
        }

        $validated = $request->validate($rules);
        $cartonCostingId = $validated['carton_costing_id'] ?? null;
        unset($validated['carton_costing_id']);

        $validated['layers'] = array_values($validated['layers']);
        $validated['ply'] = $ply;

        $calculation = $this->performCalculation($validated, $layerTemplates[$ply]);

        $attributes = [
            'customer_id' => (int) $validated['customer_id'],
            'user_id' => Auth::id(),
            'fefco_code' => $validated['fefco_code'],
            'ply' => $ply,
            'length' => (float) $validated['length'],
            'width' => (float) $validated['width'],
            'height' => (float) $validated['height'],
            'wastage_rate' => (float) $validated['wastage_rate'],
            'overhead_rate' => (float) $validated['overhead_rate'],
            'profit_rate' => (float) $validated['profit_rate'],
            'sheet_width' => $calculation['sheet_width'],
            'sheet_length' => $calculation['sheet_length'],
            'sheet_width_m' => $calculation['sheet_width_m'],
            'sheet_length_m' => $calculation['sheet_length_m'],
            'sheet_area' => $calculation['sheet_area'],
            'total_paper_cost' => $calculation['total_paper_cost'],
            'wastage_amount' => $calculation['wastage_amount'],
            'cost_after_wastage' => $calculation['cost_after_wastage'],
            'overhead_amount' => $calculation['overhead'],
            'cost_before_profit' => $calculation['cost_before_profit'],
            'profit_amount' => $calculation['profit'],
            'final_carton_cost' => $calculation['final_carton_cost'],
            'layers' => $calculation['layers'],
        ];

        if ($cartonCostingId) {
            $cartonCosting = CartonCosting::findOrFail($cartonCostingId);
            $cartonCosting->update($attributes);
            $message = 'Carton costing updated successfully.';
        } else {
            CartonCosting::create($attributes);
            $message = 'Carton costing saved successfully.';
        }

        return redirect()->route('carton_costing.report')->with('success', $message);
    }

    public function report(Request $request): View
    {
        $customers = Customer::orderBy('name')->get();

        $query = CartonCosting::with(['customer', 'user'])->orderByDesc('created_at');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('fefco_code', 'like', "%$search%")
                    ->orWhere('final_carton_cost', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%");
            });
        }

        $costings = $query->paginate(15)->withQueryString();

        return view('carton_costing.report', [
            'costings' => $costings,
            'customers' => $customers,
            'filters' => [
                'customer_id' => $request->input('customer_id'),
                'search' => $request->input('search'),
            ],
        ]);
    }

    public function edit(CartonCosting $cartonCosting): View
    {
        $layerTemplates = $this->getLayerTemplates();
        $formData = $this->mapModelToFormData($cartonCosting, $layerTemplates);
        $result = $this->buildResultFromModel($cartonCosting);

        return view('carton_costing.index', [
            'fefcoCodes' => $this->getFefcoCodes(),
            'layerTemplates' => $layerTemplates,
            'formData' => $formData,
            'fluteFactors' => self::FLUTE_FACTORS,
            'result' => $result,
            'customers' => Customer::orderBy('name')->get(),
            'editMode' => true,
            'cartonCosting' => $cartonCosting,
        ]);
    }

    public function print(CartonCosting $cartonCosting): View
    {
        $cartonCosting->load(['customer', 'user']);

        return view('carton_costing.print', [
            'costing' => $cartonCosting,
            'result' => $this->buildResultFromModel($cartonCosting),
            'layerLabels' => $this->getLayerTemplates()[$cartonCosting->ply] ?? [],
        ]);
    }

    public function destroy(CartonCosting $cartonCosting): RedirectResponse
    {
        $cartonCosting->delete();

        return redirect()->route('carton_costing.report')->with('success', 'Carton costing deleted successfully.');
    }

    private function performCalculation(array $data, array $layerTemplate): array
    {
        $ply = (int) $data['ply'];
        $sheetWidth = ($data['width'] + $data['height']) + self::PLY_ALLOWANCES[$ply];
        $sheetLength = (($data['length'] + $data['width']) * 2) + 75;

        $sheetWidthMeters = $sheetWidth / 1000;
        $sheetLengthMeters = $sheetLength / 1000;
        $sheetArea = $sheetWidthMeters * $sheetLengthMeters;

        $layerResults = [];
        $totalPaperCost = 0.0;

        foreach ($data['layers'] as $index => $layerInput) {
            $gsm = (float) $layerInput['gsm'];
            $rate = (float) $layerInput['rate'];
            $rateExcludingGst = $rate / 1.18;
            $weightKg = ($gsm * $sheetArea) / 1000;

            $isFlute = $layerTemplate[$index]['is_flute'];
            $fluteFactor = $isFlute ? self::FLUTE_FACTORS[$layerInput['flute']] : 1.0;

            $adjustedWeight = $weightKg * $fluteFactor;
            $layerCost = $adjustedWeight * $rateExcludingGst;

            $layerResults[] = [
                'quality' => $layerInput['quality'],
                'gsm' => $gsm,
                'rate' => $rate,
                'flute' => $isFlute ? $layerInput['flute'] : null,
                'flute_factor' => $fluteFactor,
                'weight_kg' => $weightKg,
                'adjusted_weight' => $adjustedWeight,
                'rate_excl_gst' => $rateExcludingGst,
                'cost' => $layerCost,
            ];

            $totalPaperCost += $layerCost;
        }

        $wastageRatePercent = array_key_exists('wastage_rate', $data)
            ? (float) $data['wastage_rate']
            : self::DEFAULT_WASTAGE_RATE * 100;
        $overheadRatePercent = array_key_exists('overhead_rate', $data)
            ? (float) $data['overhead_rate']
            : self::DEFAULT_OVERHEAD_RATE * 100;
        $profitRatePercent = array_key_exists('profit_rate', $data)
            ? (float) $data['profit_rate']
            : self::DEFAULT_PROFIT_RATE * 100;

        $wastageRate = $wastageRatePercent / 100;
        $overheadRate = $overheadRatePercent / 100;
        $profitRate = $profitRatePercent / 100;

        $wastageAmount = $totalPaperCost * $wastageRate;
        $costAfterWastage = $totalPaperCost + $wastageAmount;
        $overhead = $costAfterWastage * $overheadRate;
        $costBeforeProfit = $costAfterWastage + $overhead;
        $profit = $costBeforeProfit * $profitRate;
        $finalCartonCost = $costBeforeProfit + $profit;

        return [
            'sheet_width' => $sheetWidth,
            'sheet_length' => $sheetLength,
            'sheet_width_m' => $sheetWidthMeters,
            'sheet_length_m' => $sheetLengthMeters,
            'sheet_area' => $sheetArea,
            'layers' => $layerResults,
            'total_paper_cost' => $totalPaperCost,
            'wastage_amount' => $wastageAmount,
            'cost_after_wastage' => $costAfterWastage,
            'overhead' => $overhead,
            'cost_before_profit' => $costBeforeProfit,
            'profit' => $profit,
            'final_carton_cost' => $finalCartonCost,
            'wastage_rate_percent' => $wastageRatePercent,
            'overhead_rate_percent' => $overheadRatePercent,
            'profit_rate_percent' => $profitRatePercent,
        ];
    }

    private function getDefaultFormData(int $ply, array $templates = null): array
    {
        $templates ??= $this->getLayerTemplates();

        return [
            'carton_costing_id' => null,
            'customer_id' => null,
            'fefco_code' => null,
            'ply' => $ply,
            'length' => null,
            'width' => null,
            'height' => null,
            'wastage_rate' => self::DEFAULT_WASTAGE_RATE * 100,
            'overhead_rate' => self::DEFAULT_OVERHEAD_RATE * 100,
            'profit_rate' => self::DEFAULT_PROFIT_RATE * 100,
            'layers' => array_map(static fn ($_) => [
                'quality' => null,
                'gsm' => null,
                'rate' => null,
                'flute' => null,
            ], $templates[$ply]),
        ];
    }

    private function mapModelToFormData(CartonCosting $cartonCosting, array $templates): array
    {
        $ply = (int) $cartonCosting->ply;
        $layers = [];
        $storedLayers = $cartonCosting->layers ?? [];

        foreach ($templates[$ply] as $index => $template) {
            $layerData = $storedLayers[$index] ?? [];

            $layers[] = [
                'quality' => $layerData['quality'] ?? null,
                'gsm' => $layerData['gsm'] ?? null,
                'rate' => $layerData['rate'] ?? null,
                'flute' => $template['is_flute'] ? ($layerData['flute'] ?? null) : null,
            ];
        }

        return [
            'carton_costing_id' => $cartonCosting->id,
            'customer_id' => $cartonCosting->customer_id,
            'fefco_code' => $cartonCosting->fefco_code,
            'ply' => $ply,
            'length' => $cartonCosting->length,
            'width' => $cartonCosting->width,
            'height' => $cartonCosting->height,
            'wastage_rate' => $cartonCosting->wastage_rate,
            'overhead_rate' => $cartonCosting->overhead_rate,
            'profit_rate' => $cartonCosting->profit_rate,
            'layers' => $layers,
        ];
    }

    private function buildResultFromModel(CartonCosting $cartonCosting): array
    {
        return [
            'sheet_width' => (float) $cartonCosting->sheet_width,
            'sheet_length' => (float) $cartonCosting->sheet_length,
            'sheet_width_m' => (float) $cartonCosting->sheet_width_m,
            'sheet_length_m' => (float) $cartonCosting->sheet_length_m,
            'sheet_area' => (float) $cartonCosting->sheet_area,
            'layers' => $cartonCosting->layers ?? [],
            'total_paper_cost' => (float) $cartonCosting->total_paper_cost,
            'wastage_amount' => (float) $cartonCosting->wastage_amount,
            'cost_after_wastage' => (float) $cartonCosting->cost_after_wastage,
            'overhead' => (float) $cartonCosting->overhead_amount,
            'cost_before_profit' => (float) $cartonCosting->cost_before_profit,
            'profit' => (float) $cartonCosting->profit_amount,
            'final_carton_cost' => (float) $cartonCosting->final_carton_cost,
            'wastage_rate_percent' => (float) $cartonCosting->wastage_rate,
            'overhead_rate_percent' => (float) $cartonCosting->overhead_rate,
            'profit_rate_percent' => (float) $cartonCosting->profit_rate,
        ];
    }

    private function getLayerTemplates(): array
    {
        return [
            3 => [
                ['label' => 'Outer Liner', 'is_flute' => false],
                ['label' => 'Fluting Medium', 'is_flute' => true],
                ['label' => 'Inner Liner', 'is_flute' => false],
            ],
            5 => [
                ['label' => 'Outer Liner', 'is_flute' => false],
                ['label' => 'First Fluting Medium', 'is_flute' => true],
                ['label' => 'Middle Liner', 'is_flute' => false],
                ['label' => 'Second Fluting Medium', 'is_flute' => true],
                ['label' => 'Inner Liner', 'is_flute' => false],
            ],
            7 => [
                ['label' => 'Outer Liner', 'is_flute' => false],
                ['label' => 'First Fluting Medium', 'is_flute' => true],
                ['label' => 'Second Liner', 'is_flute' => false],
                ['label' => 'Second Fluting Medium', 'is_flute' => true],
                ['label' => 'Third Liner', 'is_flute' => false],
                ['label' => 'Third Fluting Medium', 'is_flute' => true],
                ['label' => 'Inner Liner', 'is_flute' => false],
            ],
        ];
    }

    private function getFefcoCodes(): array
    {
        return [
            '0201' => 'FEFCO 0201 - Regular Slotted Container',
            '0203' => 'FEFCO 0203 - Overlap Slotted Container',
            '0401' => 'FEFCO 0401 - Wrap Around Blank',
            '0501' => 'FEFCO 0501 - Five Panel Folder',
        ];
    }
}

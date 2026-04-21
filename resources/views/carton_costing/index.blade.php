@extends('layouts.app')

@section('title', 'Carton Costing')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
            <div>
                <h1 class="display-5 mb-2 mb-md-0"><i class="fas fa-cube me-2"></i>Carton Costing</h1>
                <p class="text-muted mb-0">Calculate carton sheet sizing and per-carton cost for 3, 5, or 7 ply configurations.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="{{ route('carton_costing.report') }}" class="btn btn-outline-secondary"><i class="fas fa-list me-2"></i>View Costing Records</a>
                @if ($editMode && $cartonCosting)
                    <a href="{{ route('carton_costing.print', $cartonCosting) }}" class="btn btn-outline-primary" target="_blank"><i class="fas fa-print me-2"></i>Print</a>
                @endif
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <h6 class="fw-bold"><i class="fas fa-circle-exclamation me-2"></i>Validation Errors</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Input Parameters</h5>
    </div>
    <div class="card-body">
        <form id="carton-costing-form" method="POST" action="{{ route('carton_costing.calculate') }}">
            @csrf
            <input type="hidden" name="carton_costing_id" value="{{ old('carton_costing_id', $formData['carton_costing_id']) }}">
            <div class="row g-3 mb-3">
            <div class="row g-3 mb-3 align-items-end">
                <div class="col-md-3">
                    <label for="fefco_code" class="form-label">FEFCO Code</label>
                    <select class="form-select" id="fefco_code" name="fefco_code" required>
                        <option value="" disabled {{ empty($formData['fefco_code']) ? 'selected' : '' }}>Select FEFCO Code</option>
                        @foreach ($fefcoCodes as $code => $description)
                            <option value="{{ $code }}" {{ $formData['fefco_code'] === $code ? 'selected' : '' }}>
                                {{ $code }} &mdash; {{ $description }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select class="form-select" id="customer_id" name="customer_id">
                        <option value="" disabled {{ empty($formData['customer_id']) ? 'selected' : '' }}>Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (int) $formData['customer_id'] === (int) $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ply" class="form-label">Number of Plies</label>
                    <select class="form-select" id="ply" name="ply" required>
                        @foreach ($layerTemplates as $plyValue => $layers)
                            <option value="{{ $plyValue }}" {{ (int) $formData['ply'] === (int) $plyValue ? 'selected' : '' }}>{{ $plyValue }} Ply</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="uom" class="form-label">UOM (Size Input)</label>
                    <select class="form-select" id="uom" name="uom">
                        <option value="mm" selected>Millimeters (mm)</option>
                        <option value="cm">Centimeters (cm)</option>
                        <option value="inch">Inches (Inch)</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-12">
                    <label class="form-label">Flute Factors</label>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        @foreach ($fluteFactors as $flute => $factor)
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <span class="input-group-text">Flute {{ $flute }}</span>
                                <input type="number" class="form-control flute-factor-input" 
                                       name="flute_factors[{{ $flute }}]" 
                                       data-flute="{{ $flute }}" 
                                       value="{{ old("flute_factors.$flute", $formData['flute_factors'][$flute] ?? $factor) }}" 
                                       step="0.01" min="1" required>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="length" class="form-label">Carton Length</label>
                    <input type="number" class="form-control" id="length" name="length" value="{{ old('length', $formData['length']) }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-4">
                    <label for="width" class="form-label">Carton Width</label>
                    <input type="number" class="form-control" id="width" name="width" value="{{ old('width', $formData['width']) }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-4">
                    <label for="height" class="form-label">Carton Height</label>
                    <input type="number" class="form-control" id="height" name="height" value="{{ old('height', $formData['height']) }}" step="0.01" min="0" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="reel_size_calc" class="form-label">Suggested Reel Size (Inch)</label>
                    <input type="text" class="form-control bg-light" id="reel_size_calc" readonly placeholder="Auto-calculated">
                </div>
                <div class="col-md-3">
                    <label for="sheet_size_calc" class="form-label">Suggested Sheet Size (Inch)</label>
                    <input type="text" class="form-control bg-light" id="sheet_size_calc" readonly placeholder="Auto-calculated">
                </div>
                <div class="col-md-3">
                    <label for="deckle_size_input" class="form-label">Deckle Size (Inch)</label>
                    <input type="number" class="form-control border-primary" id="deckle_size_input" name="deckle_size" value="{{ old('deckle_size', $formData['deckle_size'] ?? '') }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-3">
                    <label for="sheet_length_input" class="form-label">Sheet Length (Inch)</label>
                    <input type="number" class="form-control border-primary" id="sheet_length_input" name="sheet_length" value="{{ old('sheet_length', $formData['sheet_length'] ?? '') }}" step="0.01" min="0" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="ups" class="form-label">UPS (Cartons per Sheet)</label>
                    <input type="number" class="form-control border-primary" id="ups" name="ups" value="{{ old('ups', $formData['ups'] ?? 1) }}" min="1" step="1" required>
                </div>
                <div class="col-md-3">
                    <label for="paper_tax_rate" class="form-label">Tax Percent on Paper (%)</label>
                    <div class="input-group">
                        <input type="number" class="form-control border-primary" id="paper_tax_rate" name="paper_tax_rate" value="{{ old('paper_tax_rate', $formData['paper_tax_rate'] ?? 18) }}" step="0.01" min="0" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Paper Layers</h5>
            <p class="text-muted">Provide paper details for each layer. Quality selection and properties.</p>
            <div id="layer-fields"></div>

            <div class="card mb-4 mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Additional Cost Parameters</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="wastage_rate" class="form-label">Wastage %</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="wastage_rate" name="wastage_rate" value="{{ old('wastage_rate', $formData['wastage_rate']) }}" step="0.01" min="0" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="overhead_rate" class="form-label">Overhead %</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="overhead_rate" name="overhead_rate" value="{{ old('overhead_rate', $formData['overhead_rate']) }}" step="0.01" min="0" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="profit_rate" class="form-label">Profit %</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="profit_rate" name="profit_rate" value="{{ old('profit_rate', $formData['profit_rate']) }}" step="0.01" min="0" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="separator_cost" class="form-label">Separator Cost (₨)</label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" class="form-control" id="separator_cost" name="separator_cost" value="{{ old('separator_cost', $formData['separator_cost'] ?? 0) }}" step="0.01" min="0" required>
                            </div>
                            <div class="form-text">Per carton add-on cost.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="honeycomb_cost" class="form-label">Honeycomb Cost (₨)</label>
                            <div class="input-group">
                                <span class="input-group-text">₨</span>
                                <input type="number" class="form-control" id="honeycomb_cost" name="honeycomb_cost" value="{{ old('honeycomb_cost', $formData['honeycomb_cost'] ?? 0) }}" step="0.01" min="0" required>
                            </div>
                            <div class="form-text">Per carton add-on cost.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row gap-2 justify-content-end">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-calculator me-2"></i>Calculate Cost
                </button>
                <button type="submit" class="btn btn-primary" formaction="{{ route('carton_costing.store') }}">
                    <i class="fas fa-save me-2"></i>{{ $editMode ? 'Update Costing' : 'Save Costing' }}
                </button>
            </div>
        </form>
    </div>
</div>

@if ($result)
    <div class="row">
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-ruler-combined me-2"></i>Sheet Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Sheet Width (mm)</dt>
                        <dd class="col-sm-6 text-end">{{ number_format($result['sheet_width'], 2) }}</dd>

                        <dt class="col-sm-6">Sheet Length (mm)</dt>
                        <dd class="col-sm-6 text-end">{{ number_format($result['sheet_length'], 2) }}</dd>

                        <dt class="col-sm-6">Sheet Width (m)</dt>
                        <dd class="col-sm-6 text-end">{{ number_format($result['sheet_width_m'], 4) }}</dd>

                        <dt class="col-sm-6">Sheet Length (m)</dt>
                        <dd class="col-sm-6 text-end">{{ number_format($result['sheet_length_m'], 4) }}</dd>

                        <dt class="col-sm-6">Sheet Area (m²)</dt>
                        <dd class="col-sm-6 text-end">{{ number_format($result['sheet_area'], 4) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Layer Costs</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Layer</th>
                                    <th>Quality</th>
                                    <th class="text-end">GSM</th>
                                    <th class="text-end">Rate (₨/kg)</th>
                                    <th class="text-end">Flute</th>
                                    <th class="text-end">Cost (₨)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result['layers'] as $index => $layer)
                                    <tr>
                                        <td>{{ $layerTemplates[$formData['ply']][$index]['label'] }}</td>
                                        <td>{{ $layer['quality'] }}</td>
                                        <td class="text-end">{{ number_format($layer['gsm'], 2) }}</td>
                                        <td class="text-end">{{ number_format($layer['rate'], 2) }}</td>
                                        <td class="text-end">{{ $layer['flute'] ?? '—' }}</td>
                                        <td class="text-end">{{ number_format($layer['cost'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Cost Breakdown</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-light">
                        <h6 class="text-muted">Total Paper Cost</h6>
                        <p class="fs-4 mb-0">₨{{ number_format($result['total_paper_cost'], 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-muted">Wastage ({{ number_format($result['wastage_rate_percent'], 2) }}%)</h6>
                        <p class="fs-4 mb-0">₨{{ number_format($result['wastage_amount'], 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-muted">Cost After Wastage</h6>
                        <p class="fs-4 mb-0">₨{{ number_format($result['cost_after_wastage'], 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-muted">Overhead ({{ number_format($result['overhead_rate_percent'], 2) }}%)</h6>
                        <p class="fs-4 mb-0">₨{{ number_format($result['overhead'], 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-muted">Cost Before Profit</h6>
                        <p class="fs-4 mb-0">₨{{ number_format($result['cost_before_profit'], 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-muted">Profit ({{ number_format($result['profit_rate_percent'], 2) }}%)</h6>
                        <p class="fs-4 mb-0">₨{{ number_format($result['profit'], 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-light">
                        <h6 class="text-muted">Separator Cost</h6>
                        <p class="fs-4 mb-0">₨{{ number_format($result['separator_cost'], 2) }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 h-100 bg-light">
                        <h6 class="text-muted">Honeycomb Cost</h6>
                        <p class="fs-4 mb-0">₨{{ number_format($result['honeycomb_cost'], 2) }}</p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">Each sheet produces <strong>{{ $result['ups'] }}</strong> carton(s).</p>
                    <p class="mb-0 text-muted">Total Sheet Cost: ₨{{ number_format($result['final_sheet_cost'], 2) }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h4 class="mb-0">Final Carton Cost: <span class="text-success">₨{{ number_format($result['final_carton_cost'], 2) }}</span></h4>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const templates = @json($layerTemplates);
        const resultData = {
            ply: {{ $result ? (int) $formData['ply'] : 'null' }},
            layers: @json($result['layers'] ?? []),
        };

        function getFluteFactors() {
            const factors = {};
            document.querySelectorAll('.flute-factor-input').forEach(input => {
                factors[input.dataset.flute] = parseFloat(input.value) || 1.0;
            });
            return factors;
        }

        const fluteFactorInputs = document.querySelectorAll('.flute-factor-input');

        const plySelect = document.getElementById('ply');
        const layerContainer = document.getElementById('layer-fields');
        const savedLayers = {};

        let currentPly = Number(plySelect.value);
        const initialLayers = @json($formData['layers'] ?? []);
        if (Array.isArray(initialLayers) && initialLayers.length) {
            savedLayers[currentPly] = initialLayers;
        }

        const defaultFlute = Object.keys(getFluteFactors())[0] || null;

        function getDefaultLayers(ply) {
            return templates[ply].map(layer => ({
                quality: '',
                gsm: '',
                rate: '',
                flute: layer.is_flute ? defaultFlute : null,
            }));
        }

        function captureCurrentLayers() {
            const rows = layerContainer.querySelectorAll('[data-layer-index]');
            const values = [];
            rows.forEach(row => {
                const quality = row.querySelector('[data-field="quality"]').value;
                const gsm = row.querySelector('[data-field="gsm"]').value;
                const rate = row.querySelector('[data-field="rate"]').value;
                const fluteField = row.querySelector('[data-field="flute"]');
                values.push({
                    quality,
                    gsm,
                    rate,
                    flute: fluteField ? fluteField.value : null,
                });
            });
            savedLayers[currentPly] = values;
        }

        function calculateLayerAmount(row) {
            const deckleInch = parseFloat(document.getElementById('deckle_size_input').value) || 0;
            const sheetLengthInch = parseFloat(document.getElementById('sheet_length_input').value) || 0;
            const paperTaxRate = parseFloat(document.getElementById('paper_tax_rate').value) || 0;
            
            if (deckleInch <= 0 || sheetLengthInch <= 0) return;

            const paperTaxFactor = 1 + (paperTaxRate / 100);

            // Sheet Area in square meters: (W_inch * 25.4 / 1000) * (L_inch * 25.4 / 1000)
            const sheetAreaM2 = (deckleInch * 25.4 / 1000) * (sheetLengthInch * 25.4 / 1000);
            
            const gsm = parseFloat(row.querySelector('[data-field="gsm"]').value) || 0;
            const rate = parseFloat(row.querySelector('[data-field="rate"]').value) || 0;
            const fluteField = row.querySelector('[data-field="flute"]');
            const fluteValue = fluteField ? fluteField.value : null;
            const currentFactors = getFluteFactors();
            const factor = fluteValue ? (currentFactors[fluteValue] || 1.0) : 1.0;
            
            if (gsm > 0 && rate > 0) {
                // Formula: layerCost = (weightKg * factor) * (rate / TaxFactor)
                const rateExcludingTax = rate / paperTaxFactor;
                const weightKg = (gsm * sheetAreaM2) / 1000;
                const adjustedWeight = weightKg * factor;
                const amount = adjustedWeight * rateExcludingTax;
                
                row.querySelector('.layer-amount-input').value = amount.toFixed(2);
            } else {
                row.querySelector('.layer-amount-input').value = '';
            }
        }

        function renderLayers(ply) {
            currentPly = ply;
            const template = templates[ply];
            let layerData = savedLayers[ply];

            if (!layerData || layerData.length !== template.length) {
                layerData = getDefaultLayers(ply);
                savedLayers[ply] = layerData;
            }

            layerContainer.innerHTML = '';

            template.forEach((layer, index) => {
                const data = layerData[index] || { quality: '', gsm: '', rate: '', flute: layer.is_flute ? defaultFlute : null };
                if (layer.is_flute && !data.flute) {
                    data.flute = defaultFlute;
                }
                const resultLayer = (resultData.ply === ply && resultData.layers[index]) ? resultData.layers[index] : null;
                const amountValue = resultLayer ? Number(resultLayer.cost).toFixed(2) : '';

                const currentFactors = getFluteFactors();
                const card = document.createElement('div');
                card.className = 'card mb-3';
                card.dataset.layerIndex = index;

                const fluteOptions = layer.is_flute
                    ? Object.keys(currentFactors).map(flute => `<option value="${flute}" ${data.flute === flute ? 'selected' : ''}>${flute} (×${currentFactors[flute].toFixed(2)})</option>`).join('')
                    : '';

                const fluteMarkup = layer.is_flute
                    ? `<div class="col-md-2">
                            <label class="form-label">Flute</label>
                            <select class="form-select layer-calc-trigger" name="layers[${index}][flute]" data-field="flute" required>
                                ${fluteOptions}
                            </select>
                       </div>`
                    : `<input type="hidden" name="layers[${index}][flute]" value="" data-field="flute">`;

                card.innerHTML = `
                    <div class="card-header bg-light py-2 fw-semibold d-flex justify-content-between">
                        <span>${layer.label}</span>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Paper Quality</label>
                                <input type="text" class="form-control" name="layers[${index}][quality]" data-field="quality" value="${data.quality ?? ''}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1">GSM</label>
                                <input type="number" class="form-control layer-calc-trigger" name="layers[${index}][gsm]" data-field="gsm" value="${data.gsm ?? ''}" min="0" step="0.1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1">Rate (₨/kg)</label>
                                <input type="number" class="form-control layer-calc-trigger" name="layers[${index}][rate]" data-field="rate" value="${data.rate ?? ''}" min="0" step="0.01" required>
                            </div>
                            ${fluteMarkup}
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Amount (₨)</label>
                                <input type="text" class="form-control layer-amount-input bg-light" value="${amountValue}" placeholder="0.00" readonly>
                            </div>
                        </div>
                    </div>
                `;

                // Single-row layout check: Quality(3) + GSM(2) + Rate(2) + Flute(2) + Amount(3) = 12 columns total. Perfect.

                layerContainer.appendChild(card);
            });

            // Trigger calculations for all rows
            layerContainer.querySelectorAll('[data-layer-index]').forEach(row => {
                row.querySelectorAll('.layer-calc-trigger').forEach(input => {
                    input.addEventListener('input', () => calculateLayerAmount(row));
                });
            });
        }

        const lengthInput = document.getElementById('length');
        const widthInput = document.getElementById('width');
        const heightInput = document.getElementById('height');
        const uomSelect = document.getElementById('uom');
        const reelCalcInput = document.getElementById('reel_size_calc');
        const sheetCalcInput = document.getElementById('sheet_size_calc');

        function updateDynamicCalculations() {
            const L = parseFloat(lengthInput.value) || 0;
            const W = parseFloat(widthInput.value) || 0;
            const H = parseFloat(heightInput.value) || 0;
            const ply = parseInt(plySelect.value);
            const uom = uomSelect.value;

            if (L <= 0 || W <= 0 || H <= 0) {
                reelCalcInput.value = '';
                sheetCalcInput.value = '';
                return;
            }

            // Conversion factors to mm
            let factor = 1;
            if (uom === 'cm') factor = 10;
            if (uom === 'inch') factor = 25.4;

            const L_mm = L * factor;
            const W_mm = W * factor;
            const H_mm = H * factor;

            // Formulas in mm
            let deckleAddition = 0;
            if (ply === 3) deckleAddition = 12;
            else if (ply === 5) deckleAddition = 20;
            else if (ply === 7) deckleAddition = 25;

            const reelSizeMm = W_mm + H_mm + deckleAddition;
            const sheetLengthMm = 2 * (L_mm + W_mm) + 75;

            // Display in inches
            reelCalcInput.value = (reelSizeMm / 25.4).toFixed(2);
            sheetCalcInput.value = (sheetLengthMm / 25.4).toFixed(2);
        }

        [lengthInput, widthInput, heightInput, uomSelect, plySelect, 
         document.getElementById('deckle_size_input'), 
         document.getElementById('sheet_length_input'),
         document.getElementById('paper_tax_rate'),
         ...fluteFactorInputs].forEach(el => {
            el.addEventListener('input', () => {
                updateDynamicCalculations();
                // When dimensions or tax or flute factors change, update all layer amounts as well
                layerContainer.querySelectorAll('[data-layer-index]').forEach(calculateLayerAmount);
                
                // If flute factors change, also update the multiplier labels in dropdowns
                if (el.classList.contains('flute-factor-input')) {
                    const factors = getFluteFactors();
                    document.querySelectorAll('select[data-field="flute"]').forEach(select => {
                        const currentVal = select.value;
                        const flute = el.dataset.flute;
                        const option = select.querySelector(`option[value="${flute}"]`);
                        if (option) {
                            option.textContent = `${flute} (×${factors[flute].toFixed(2)})`;
                        }
                    });
                }
            });
        });

        plySelect.addEventListener('change', event => {
            captureCurrentLayers();
            const nextPly = Number(event.target.value);
            renderLayers(nextPly);
        });

        // Initialize calculations
        updateDynamicCalculations();

        renderLayers(currentPly);
    });
</script>
@endsection

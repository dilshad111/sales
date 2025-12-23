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
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select class="form-select" id="customer_id" name="customer_id">
                        <option value="" disabled {{ empty($formData['customer_id']) ? 'selected' : '' }}>Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (int) $formData['customer_id'] === (int) $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="ply" class="form-label">Number of Plies</label>
                    <select class="form-select" id="ply" name="ply" required>
                        @foreach ($layerTemplates as $plyValue => $layers)
                            <option value="{{ $plyValue }}" {{ (int) $formData['ply'] === (int) $plyValue ? 'selected' : '' }}>{{ $plyValue }} Ply</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="flute_info" class="form-label">Flute Factors</label>
                    <div class="form-control" id="flute_info" readonly>
                        @foreach ($fluteFactors as $flute => $factor)
                            <span class="badge bg-secondary me-2">{{ $flute }} = {{ number_format($factor, 2) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="length" class="form-label">Carton Length (mm)</label>
                    <input type="number" class="form-control" id="length" name="length" value="{{ old('length', $formData['length']) }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-4">
                    <label for="width" class="form-label">Carton Width (mm)</label>
                    <input type="number" class="form-control" id="width" name="width" value="{{ old('width', $formData['width']) }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-4">
                    <label for="height" class="form-label">Carton Height (mm)</label>
                    <input type="number" class="form-control" id="height" name="height" value="{{ old('height', $formData['height']) }}" step="0.01" min="0" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
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

            <h5 class="mb-3">Paper Layers</h5>
            <p class="text-muted">Provide paper details for each layer. Amount fields are calculated after submitting the form.</p>
            <div id="layer-fields"></div>

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
            </div>
            <hr>
            <div class="text-end">
                <h4 class="mb-0">Final Carton Cost: <span class="text-success">₨{{ number_format($result['final_carton_cost'], 2) }}</span></h4>
            </div>
        </div>
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const templates = @json($layerTemplates);
        const fluteFactors = @json($fluteFactors);
        const resultData = {
            ply: {{ $result ? (int) $formData['ply'] : 'null' }},
            layers: @json($result['layers'] ?? []),
        };

        const plySelect = document.getElementById('ply');
        const layerContainer = document.getElementById('layer-fields');
        const savedLayers = {};

        let currentPly = Number(plySelect.value);
        const initialLayers = @json($formData['layers'] ?? []);
        if (Array.isArray(initialLayers) && initialLayers.length) {
            savedLayers[currentPly] = initialLayers;
        }

        const defaultFlute = Object.keys(fluteFactors)[0] || null;

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

                const card = document.createElement('div');
                card.className = 'card mb-3';
                card.dataset.layerIndex = index;

                const fluteOptions = layer.is_flute
                    ? Object.keys(fluteFactors).map(flute => `<option value="${flute}" ${data.flute === flute ? 'selected' : ''}>${flute} (×${fluteFactors[flute].toFixed(2)})</option>`).join('')
                    : '';

                const fluteMarkup = layer.is_flute
                    ? `<div class="col-md-3">
                            <label class="form-label">Flute</label>
                            <select class="form-select" name="layers[${index}][flute]" data-field="flute" required>
                                ${fluteOptions}
                            </select>
                       </div>`
                    : `<input type="hidden" name="layers[${index}][flute]" value="">`;

                card.innerHTML = `
                    <div class="card-header bg-light fw-semibold">${layer.label}</div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Paper Quality</label>
                                <input type="text" class="form-control" name="layers[${index}][quality]" data-field="quality" value="${data.quality ?? ''}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">GSM</label>
                                <input type="number" class="form-control" name="layers[${index}][gsm]" data-field="gsm" value="${data.gsm ?? ''}" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Rate (₨/kg)</label>
                                <input type="number" class="form-control" name="layers[${index}][rate]" data-field="rate" value="${data.rate ?? ''}" min="0" step="0.01" required>
                            </div>
                            ${fluteMarkup}
                            <div class="col-md-3">
                                <label class="form-label">Amount (₨)</label>
                                <input type="text" class="form-control" value="${amountValue}" placeholder="Calculated" disabled>
                            </div>
                        </div>
                    </div>
                `;

                layerContainer.appendChild(card);
            });
        }

        plySelect.addEventListener('change', event => {
            captureCurrentLayers();
            const nextPly = Number(event.target.value);
            renderLayers(nextPly);
        });

        renderLayers(currentPly);
    });
</script>
@endsection

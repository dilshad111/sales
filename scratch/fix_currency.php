<?php

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Cache;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$setting = CompanySetting::first();
if ($setting) {
    echo "Current Symbol: " . $setting->currency_symbol . PHP_EOL;
    $setting->update(['currency_symbol' => 'Rs.']);
    Cache::forget('company_setting');
    Cache::forever('company_setting', $setting);
    echo "Updated Symbol to: Rs." . PHP_EOL;
} else {
    CompanySetting::create([
        'name' => 'Sales ERP',
        'currency_symbol' => 'Rs.',
        'address' => 'Pakistan',
    ]);
    echo "Created new setting with Symbol: Rs." . PHP_EOL;
}

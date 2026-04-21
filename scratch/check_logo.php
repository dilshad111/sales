<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CompanySetting;

$company = CompanySetting::first();
echo "Logo Path: " . ($company->logo_path ?? 'NONE') . "\n";
echo "Full Company Data: \n";
print_r($company->toArray());

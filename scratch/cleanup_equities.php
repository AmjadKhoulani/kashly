<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Equity;
use App\Models\InvestmentFund;

echo "Cleaning up orphaned equities...\n";

$orphans = Equity::where('equitable_type', InvestmentFund::class)
    ->whereNotExists(function ($query) {
        $query->select(Illuminate\Support\Facades\DB::raw(1))
            ->from('investment_funds')
            ->whereRaw('investment_funds.id = equities.equitable_id');
    })->get();

foreach ($orphans as $orphan) {
    echo "Deleting orphan equity (ID: {$orphan->id})\n";
    $orphan->delete();
}

echo "Cleanup complete. " . count($orphans) . " orphans removed.\n";

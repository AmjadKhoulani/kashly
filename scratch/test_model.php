<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;

try {
    $t = new Transaction();
    if (method_exists($t, 'transactionable')) {
        echo "Relationship 'transactionable' EXISTS\n";
    } else {
        echo "Relationship 'transactionable' MISSING\n";
    }
    
    // Test if it returns a MorphTo
    $rel = $t->transactionable();
    echo "Return type: " . get_class($rel) . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

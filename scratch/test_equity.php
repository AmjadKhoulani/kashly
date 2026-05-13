<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Equity;

try {
    $e = new Equity();
    if (method_exists($e, 'partner')) {
        echo "Relationship 'partner' EXISTS\n";
    } else {
        echo "Relationship 'partner' MISSING\n";
    }
    
    $rel = $e->partner();
    echo "Return type: " . get_class($rel) . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

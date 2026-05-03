<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$p = App\Models\Payment::query()->find(2);
if (!$p) { echo 'NOT_FOUND'; exit; }
echo 'method='.$p->payment_method.PHP_EOL;
echo 'status='.$p->payment_status.PHP_EOL;

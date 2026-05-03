<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$p = App\Models\Payment::query()->find(8);
if (!$p) { echo "PAYMENT_NOT_FOUND\n"; exit; }
$updated = app(App\Services\PaymentService::class)->syncFromMidtransGateway($p, 3, 500);
echo 'PAYMENT_STATUS='.$updated->payment_status.PHP_EOL;
echo 'MIDTRANS_STATUS_CODE='.$updated->midtrans_status_code.PHP_EOL;
echo 'BOOKING_STATUS='.$updated->booking->fresh()->status.PHP_EOL;

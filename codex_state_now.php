<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$booking = App\Models\Booking::query()->where('booking_code','BK-20260503132430-518')->with('payments')->first();
if (!$booking) { echo "BOOKING_NOT_FOUND\n"; exit; }
echo 'BOOKING_STATUS='.$booking->status.PHP_EOL;
foreach ($booking->payments->sortBy('id') as $p) {
  echo 'PAYMENT#'.$p->id.' status='.$p->payment_status.' method='.$p->payment_method.' order='.$p->midtrans_order_id.' created='.$p->created_at.PHP_EOL;
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$p = App\Models\Payment::query()->find(2);
if (!$p) { echo "PAYMENT_NOT_FOUND\n"; exit; }
$key=(string) config('services.midtrans.server_key');
$base=(bool) config('services.midtrans.is_production', false) ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
$res = Illuminate\Support\Facades\Http::acceptJson()->withBasicAuth($key, '')->withOptions(['verify'=>(bool)config('services.midtrans.verify_ssl',true)])->get($base.'/v2/'.$p->midtrans_order_id.'/status');
$j=(array)$res->json();
echo 'order='.$p->midtrans_order_id.PHP_EOL;
echo 'http='.$res->status().PHP_EOL;
echo 'status_code='.(string)($j['status_code'] ?? '').PHP_EOL;
echo 'status_message='.(string)($j['status_message'] ?? '').PHP_EOL;
echo 'transaction_status='.(string)($j['transaction_status'] ?? '').PHP_EOL;
echo 'payment_type='.(string)($j['payment_type'] ?? '').PHP_EOL;

<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$order='MID-3-8-20260503133626';
$key=(string) config('services.midtrans.server_key');
$base=(bool) config('services.midtrans.is_production', false) ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
$res = Illuminate\Support\Facades\Http::acceptJson()->withBasicAuth($key, '')->withOptions(['verify'=>(bool)config('services.midtrans.verify_ssl',true)])->get($base.'/v2/'.$order.'/status/b2b');
$j=(array)$res->json();
echo 'http='.$res->status().PHP_EOL;
print_r($j);

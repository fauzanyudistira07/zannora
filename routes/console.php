<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('midtrans:sync-ngrok {--port=4040}', function () {
    $port = max((int) $this->option('port'), 1);

    $response = Http::timeout(5)->get("http://127.0.0.1:{$port}/api/tunnels");
    if (! $response->ok()) {
        $this->error("Gagal membaca ngrok API di port {$port}. Pastikan ngrok sedang berjalan.");

        return 1;
    }

    $tunnels = (array) $response->json('tunnels', []);
    $httpsTunnel = collect($tunnels)->first(fn (array $tunnel) => ($tunnel['proto'] ?? null) === 'https');

    if (! $httpsTunnel || empty($httpsTunnel['public_url'])) {
        $this->error('Tunnel HTTPS ngrok tidak ditemukan.');

        return 1;
    }

    $publicUrl = rtrim((string) $httpsTunnel['public_url'], '/');
    $notificationUrl = $publicUrl.'/api/v1/payments/midtrans/notification';

    $envPath = base_path('.env');
    if (! file_exists($envPath)) {
        $this->error('.env tidak ditemukan.');

        return 1;
    }

    $env = (string) file_get_contents($envPath);

    $setEnv = static function (string $key, string $value, string $currentEnv): string {
        $line = $key.'='.$value;
        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

        if (preg_match($pattern, $currentEnv) === 1) {
            return (string) preg_replace($pattern, $line, $currentEnv, 1);
        }

        return rtrim($currentEnv).PHP_EOL.$line.PHP_EOL;
    };

    $env = $setEnv('APP_URL', $publicUrl, $env);
    $env = $setEnv('MIDTRANS_NOTIFICATION_URL', $notificationUrl, $env);

    file_put_contents($envPath, $env);

    $this->call('config:clear');
    $this->call('config:cache');

    $this->info('Sinkronisasi ngrok ke Midtrans berhasil.');
    $this->line('APP_URL                   : '.$publicUrl);
    $this->line('MIDTRANS_NOTIFICATION_URL : '.$notificationUrl);

    return 0;
})->purpose('Sync ngrok HTTPS URL into APP_URL and MIDTRANS_NOTIFICATION_URL for Midtrans Sandbox testing.');

Artisan::command('midtrans:use-local {--host=127.0.0.1} {--port=8000}', function () {
    $host = trim((string) $this->option('host'));
    $port = max((int) $this->option('port'), 1);

    if ($host === '') {
        $this->error('Host tidak boleh kosong.');

        return 1;
    }

    $appUrl = "http://{$host}:{$port}";
    $envPath = base_path('.env');

    if (! file_exists($envPath)) {
        $this->error('.env tidak ditemukan.');

        return 1;
    }

    $env = (string) file_get_contents($envPath);

    $setEnv = static function (string $key, string $value, string $currentEnv): string {
        $line = $key.'='.$value;
        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

        if (preg_match($pattern, $currentEnv) === 1) {
            return (string) preg_replace($pattern, $line, $currentEnv, 1);
        }

        return rtrim($currentEnv).PHP_EOL.$line.PHP_EOL;
    };

    $env = $setEnv('APP_URL', $appUrl, $env);
    $env = $setEnv('MIDTRANS_NOTIFICATION_URL', '', $env);

    file_put_contents($envPath, $env);

    $this->call('config:clear');
    $this->call('config:cache');

    $this->info('Mode Midtrans lokal berhasil diaktifkan.');
    $this->line('APP_URL                   : '.$appUrl);
    $this->line('MIDTRANS_NOTIFICATION_URL : (kosong)');
    $this->line('Catatan: webhook Midtrans tidak akan masuk ke localhost; status payment akan disinkronkan saat user kembali ke aplikasi.');

    return 0;
})->purpose('Use local APP_URL and disable Midtrans webhook override for php artisan serve workflow.');

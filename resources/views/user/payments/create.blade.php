@extends('layouts.portal')

@section('title', 'Zannora | Payment')
@section('active', 'bookings')

@section('content')
    @php($initialMethod = old('payment_method', 'qris'))

    <section
        x-data="paymentForm({{ \Illuminate\Support\Js::from($paymentMethods) }}, '{{ $initialMethod }}')"
        class="grid gap-6 lg:grid-cols-[1fr_360px]"
    >
        <article class="portal-card">
            <h1 class="font-heading text-3xl font-bold text-[#0f3f78] sm:text-4xl">Payment Submission</h1>
            <p class="mt-1 text-slate-600">Booking {{ $booking->booking_code }} ditahan selama 5 menit. Lengkapi pembayaran sebelum waktu habis agar kursi tidak dilepas kembali.</p>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Booking Status</p>
                    <p class="font-semibold text-slate-800">{{ ucfirst($booking->status) }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Batas Pembayaran</p>
                    <p class="font-semibold text-slate-800">{{ $booking->expired_at?->format('d M Y H:i:s') ?: '-' }}</p>
                    @if ($booking->expired_at)
                        <p class="mt-1 text-sm font-semibold text-amber-600" x-text="countdown"></p>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                <div>
                    <label class="portal-label">Payment Method</label>
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach ($paymentMethods as $value => $method)
                            <label class="portal-card-soft flex cursor-pointer items-start gap-3 transition duration-200" :class="method === '{{ $value }}' ? 'ring-2 ring-[#0f3f78] border-[#0f3f78] bg-white' : 'hover:bg-white'">
                                <input type="radio" name="payment_method" value="{{ $value }}" class="mt-1 h-4 w-4 border-slate-300 text-[#0f3f78]" x-model="method">
                                <span class="min-w-0">
                                    <span class="flex items-center gap-3">
                                        <span class="grid h-10 w-10 shrink-0 place-content-center rounded-xl bg-[#0f3f78] text-xs font-bold text-white">{{ $method['icon'] }}</span>
                                        <span class="block text-base font-semibold text-slate-800">{{ $method['label'] }}</span>
                                    </span>
                                    <span class="mt-2 block text-sm text-slate-500">{{ $method['description'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="portal-card-soft" x-show="selectedMethod" x-cloak>
                    <p class="text-sm text-slate-500">Instruksi Pembayaran</p>
                    <template x-if="selectedMethod">
                        <div class="mt-2 space-y-2 text-sm text-slate-600">
                            <p class="font-semibold text-slate-800" x-text="selectedMethod.label"></p>
                            <p x-text="selectedMethod.description"></p>
                            <template x-if="selectedMethod.destination?.bank_name">
                                <p>
                                    Tujuan transfer:
                                    <span class="font-semibold text-slate-800" x-text="selectedMethod.destination.bank_name"></span>
                                    -
                                    <span class="font-semibold text-slate-800" x-text="selectedMethod.destination.account_number"></span>
                                    a/n
                                    <span class="font-semibold text-slate-800" x-text="selectedMethod.destination.account_name"></span>
                                </p>
                            </template>
                            <template x-if="!selectedMethod.destination?.bank_name && selectedMethod.destination?.account_number">
                                <p>
                                    Tujuan pembayaran:
                                    <span class="font-semibold text-slate-800" x-text="selectedMethod.destination.account_number"></span>
                                    a/n
                                    <span class="font-semibold text-slate-800" x-text="selectedMethod.destination.account_name"></span>
                                </p>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div x-show="method !== 'qris'" x-cloak>
                        <label for="payer_name" class="portal-label">Nama Pengirim / Pemilik Akun</label>
                        <input id="payer_name" name="payer_name" value="{{ old('payer_name') }}" class="portal-input" placeholder="Nama pengirim">
                        @error('payer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="selectedType === 'e_wallet' || selectedType === 'card'" x-cloak>
                        <label for="payer_phone" class="portal-label">Nomor Telepon Pembayar</label>
                        <input id="payer_phone" name="payer_phone" value="{{ old('payer_phone') }}" class="portal-input" placeholder="08xxxxxxxxxx">
                        @error('payer_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="selectedType === 'bank_transfer' || selectedType === 'virtual_account'" x-cloak>
                        <label for="payer_bank_name" class="portal-label">Bank Pengirim</label>
                        <input id="payer_bank_name" name="payer_bank_name" value="{{ old('payer_bank_name') }}" class="portal-input" placeholder="Contoh: BCA / BRI / Mandiri">
                        @error('payer_bank_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="selectedType === 'bank_transfer' || selectedType === 'virtual_account'" x-cloak>
                        <label for="payer_bank_account_number" class="portal-label">Nomor Rekening Pengirim</label>
                        <input id="payer_bank_account_number" name="payer_bank_account_number" value="{{ old('payer_bank_account_number') }}" class="portal-input" placeholder="Nomor rekening pengirim">
                        @error('payer_bank_account_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div x-show="selectedType === 'card'" x-cloak>
                    <label for="payment_notes" class="portal-label">Catatan Referensi Transaksi</label>
                    <textarea id="payment_notes" name="payment_notes" rows="3" class="portal-input" placeholder="Contoh: ref auth 9921 / 4 digit akhir kartu">{{ old('payment_notes') }}</textarea>
                    @error('payment_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-show="selectedMethod && selectedMethod.requires_proof" x-cloak>
                    <label for="proof_file" class="portal-label">Upload Bukti Pembayaran</label>
                    <input id="proof_file" name="proof_file" type="file" class="portal-input">
                    <p class="mt-2 text-sm text-slate-500">Format yang diterima: JPG, PNG, PDF. Maksimum 2MB.</p>
                    @error('proof_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="portal-btn-gold">Kirim Pembayaran</button>
                    <a href="{{ route('my-bookings.show', $booking) }}" class="portal-btn-blue">Back to Booking</a>
                </div>
            </form>
        </article>

        <aside class="portal-card h-fit">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Payment Summary</h2>
            <div class="mt-4 space-y-3">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Booking Code</p>
                    <p class="font-semibold text-slate-800">{{ $booking->booking_code }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Route</p>
                    <p class="font-semibold text-slate-800">{{ $booking->flight->departureAirport->code }} -> {{ $booking->flight->arrivalAirport->code }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Total</p>
                    <p class="text-2xl font-bold text-[#0f3f78]">Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Latest Submission</p>
                    <p class="font-semibold text-slate-800">{{ ucfirst($latestPayment?->payment_status ?? 'pending') }}</p>
                    @if ($latestPayment?->submitted_at)
                        <p class="mt-1 text-sm text-slate-500">{{ $latestPayment->submitted_at->format('d M Y H:i:s') }}</p>
                    @endif
                </div>
            </div>
        </aside>
    </section>

    <script>
        function paymentForm(methods, initialMethod) {
            return {
                methods,
                method: initialMethod,
                expiresAt: @js(optional($booking->expired_at)?->toIso8601String()),
                countdown: '',
                get selectedMethod() {
                    return this.methods[this.method] || null;
                },
                get selectedType() {
                    return this.selectedMethod?.type || null;
                },
                init() {
                    this.updateCountdown();
                    setInterval(() => this.updateCountdown(), 1000);
                },
                updateCountdown() {
                    if (!this.expiresAt) {
                        this.countdown = '';
                        return;
                    }

                    const diff = new Date(this.expiresAt).getTime() - Date.now();

                    if (diff <= 0) {
                        this.countdown = 'Sesi pembayaran telah berakhir.';
                        return;
                    }

                    const minutes = Math.floor(diff / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
                    this.countdown = `Sisa waktu ${minutes}:${seconds}`;
                },
            };
        }
    </script>
@endsection

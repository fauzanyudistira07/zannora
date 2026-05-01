@extends('layouts.portal')

@section('title', 'Zannora | Contact')
@section('active', 'contact')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[1.08fr_0.92fr]">
        <article class="portal-card">
            <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">Contact Us</p>
            <h1 class="mt-2 font-heading text-3xl font-bold text-slate-800 sm:text-4xl">Need Help With Your Booking?</h1>
            <p class="mt-4 max-w-3xl text-slate-600">
                Hubungi tim Zannora untuk pertanyaan booking, perubahan jadwal, atau bantuan pembayaran.
                Halaman ini dibuat terpisah agar akses informasi kontak terasa lebih profesional dan langsung.
            </p>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="portal-card-soft">
                    <p class="text-sm uppercase tracking-wide text-slate-500">Customer Support</p>
                    <p class="mt-2 text-xl font-semibold text-slate-800">support@zannora.test</p>
                    <p class="mt-1 text-sm text-slate-500">Response target within 15 minutes</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm uppercase tracking-wide text-slate-500">Call Center</p>
                    <p class="mt-2 text-xl font-semibold text-slate-800">0800-100-9666</p>
                    <p class="mt-1 text-sm text-slate-500">24/7 booking and payment assistance</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm uppercase tracking-wide text-slate-500">Office Hours</p>
                    <p class="mt-2 text-xl font-semibold text-slate-800">Mon - Sun</p>
                    <p class="mt-1 text-sm text-slate-500">00:00 - 23:59 WIB</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm uppercase tracking-wide text-slate-500">Head Office</p>
                    <p class="mt-2 text-xl font-semibold text-slate-800">Jakarta Operations Center</p>
                    <p class="mt-1 text-sm text-slate-500">Sudirman Business District, Jakarta</p>
                </div>
            </div>

            <div class="mt-8 rounded-[28px] border border-slate-200 bg-white/75 p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">Support Form</p>
                        <h2 class="mt-2 font-heading text-2xl font-bold text-slate-800">Send a detailed request</h2>
                        <p class="mt-2 max-w-2xl text-sm text-slate-600">Pesan yang dikirim dari form ini akan masuk ke inbox tim operasional agar bisa ditangani lebih rapi.</p>
                    </div>
                    <span class="portal-status-default">Response SLA 15 - 30 mins</span>
                </div>

                <form method="POST" action="{{ route('contact.submit') }}" class="mt-6 grid gap-4 md:grid-cols-2">
                    @csrf
                    <div>
                        <label class="portal-label" for="name">Full Name</label>
                        <input id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" class="portal-input" required>
                    </div>
                    <div>
                        <label class="portal-label" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" class="portal-input" required>
                    </div>
                    <div>
                        <label class="portal-label" for="phone">Phone</label>
                        <input id="phone" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" class="portal-input" placeholder="+62...">
                    </div>
                    <div>
                        <label class="portal-label" for="subject">Subject</label>
                        <input id="subject" name="subject" value="{{ old('subject') }}" class="portal-input" placeholder="Perubahan jadwal, refund, payment issue" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="portal-label" for="message">Message</label>
                        <textarea id="message" name="message" rows="6" class="portal-input" required>{{ old('message') }}</textarea>
                    </div>
                    <div class="md:col-span-2 flex flex-wrap items-center gap-3">
                        <button type="submit" class="portal-btn-gold">Submit Support Request</button>
                        <p class="text-sm text-slate-500">Gunakan subjek dan detail yang jelas agar staff bisa langsung menindaklanjuti.</p>
                    </div>
                </form>
            </div>
        </article>

        <aside class="portal-card h-fit">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Featured Airports</h2>
            <div class="mt-4 space-y-3">
                @foreach ($airports as $airport)
                    <div class="portal-card-soft">
                        <p class="font-semibold text-slate-800">{{ $airport->city }} ({{ $airport->code }})</p>
                        <p class="text-sm text-slate-500">{{ $airport->name }}</p>
                    </div>
                @endforeach
            </div>

            @auth
                <div class="mt-6 border-t border-slate-200 pt-6">
                    <h3 class="font-heading text-xl font-bold text-[#0f3f78]">Your Recent Support Cases</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($recentMessages as $recentMessage)
                            <div class="portal-card-soft">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $recentMessage->subject }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $recentMessage->created_at?->format('d M Y H:i') }}</p>
                                    </div>
                                    @if ($recentMessage->status === 'resolved')
                                        <span class="portal-status-confirmed">Resolved</span>
                                    @elseif ($recentMessage->status === 'closed')
                                        <span class="portal-status-cancelled">Closed</span>
                                    @elseif ($recentMessage->status === 'in_progress')
                                        <span class="portal-status-pending">In Progress</span>
                                    @else
                                        <span class="portal-status-default">Open</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="portal-card-soft text-sm text-slate-500">Belum ada case support yang Anda kirim.</div>
                        @endforelse
                    </div>
                </div>
            @endauth
        </aside>
    </section>
@endsection

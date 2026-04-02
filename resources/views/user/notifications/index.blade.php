@extends('layouts.portal')

@section('title', 'Zannora | Notifications')
@section('active', 'profile')

@section('content')
    <section>
        <h1 class="font-heading text-4xl font-bold text-white">Notifications</h1>
        <p class="mt-1 text-white/85">Booking updates, payment updates, and important travel info.</p>

        <div class="mt-6 space-y-4">
            @forelse ($notifications as $notification)
                <article class="portal-card">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-800">{{ $notification['title'] }}</h2>
                            <p class="mt-1 text-slate-600">{{ $notification['description'] }}</p>
                        </div>
                        <span class="text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($notification['time'])->format('d M Y H:i') }}</span>
                    </div>
                </article>
            @empty
                <div class="portal-card text-center text-slate-600">No notification yet.</div>
            @endforelse
        </div>
    </section>
@endsection


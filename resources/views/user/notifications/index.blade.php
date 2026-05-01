@extends('layouts.portal')

@section('title', 'Zannora | Notifications')
@section('active', 'profile')

@section('content')
    <section>
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="font-heading text-4xl font-bold text-white">Notifications</h1>
                <p class="mt-1 text-white/85">Booking updates, payment updates, support replies, and important travel info.</p>
            </div>
            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="portal-btn-blue">Mark All Read</button>
                </form>
            @endif
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($notifications as $notification)
                @php($payload = $notification->data)
                <article class="portal-card">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-semibold text-slate-800">{{ $payload['title'] }}</h2>
                                @if (blank($notification->read_at))
                                    <span class="portal-status-pending">Unread</span>
                                @endif
                            </div>
                            <p class="mt-1 text-slate-600">{{ $payload['message'] }}</p>
                            @if (! empty($payload['action_url']))
                                <a href="{{ $payload['action_url'] }}" class="mt-3 inline-flex text-sm font-semibold text-[#0f3f78] underline">Open related page</a>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="text-xs text-slate-500">{{ $notification->created_at?->format('d M Y H:i') }}</span>
                            @if (blank($notification->read_at))
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button type="submit" class="portal-btn-blue px-3 py-2 text-sm">Mark Read</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="portal-card text-center text-slate-600">No notification yet.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </section>
@endsection

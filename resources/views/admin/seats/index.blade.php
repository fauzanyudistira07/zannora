@extends('layouts.admin')

@section('title', 'Admin Seats | Zannora')
@section('page-title', 'Seats')

@section('content')
    <section class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-white/90">Monitoring seat per airplane.</p>
            <a href="{{ route('admin.seats.create') }}" class="admin-btn-primary">Tambah Seat</a>
        </div>

        <form method="GET" class="admin-card grid gap-3 md:grid-cols-5">
            <div class="md:col-span-2">
                <label class="admin-label" for="search">Seat Number</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Contoh: 12A">
            </div>
            <div>
                <label class="admin-label" for="airplane_id">Airplane</label>
                <select id="airplane_id" name="airplane_id" class="admin-field">
                    <option value="">Semua airplane</option>
                    @foreach ($airplanes as $airplane)
                        <option value="{{ $airplane->id }}" @selected((int) $airplaneId === (int) $airplane->id)>{{ $airplane->model }} ({{ $airplane->registration_number }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="class">Class</label>
                <select id="class" name="class" class="admin-field">
                    <option value="">Semua class</option>
                    <option value="economy" @selected($class === 'economy')>Economy</option>
                    <option value="business" @selected($class === 'business')>Business</option>
                    <option value="first" @selected($class === 'first')>First</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a class="admin-btn-secondary" href="{{ route('admin.seats.index') }}">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Airplane</th>
                            <th>Airline</th>
                            <th>Seat Number</th>
                            <th>Class</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($seats as $seat)
                            <tr>
                                <td>{{ $seat->airplane?->model }} ({{ $seat->airplane?->registration_number }})</td>
                                <td>{{ $seat->airplane?->airline?->name }}</td>
                                <td class="font-semibold">{{ $seat->seat_number }}</td>
                                <td>{{ ucfirst($seat->class) }}</td>
                                <td>{{ $seat->created_at?->format('d M Y') }}</td>
                                <td class="space-x-1">
                                    <a class="admin-btn-secondary" href="{{ route('admin.seats.show', $seat) }}">Detail</a>
                                    <a class="admin-btn-secondary" href="{{ route('admin.seats.edit', $seat) }}">Edit</a>
                                    <form action="{{ route('admin.seats.destroy', $seat) }}" method="POST" class="inline" onsubmit="return confirm('Hapus seat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-btn-secondary" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-500">Data seat belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $seats->links() }}</div>
        </article>
    </section>
@endsection

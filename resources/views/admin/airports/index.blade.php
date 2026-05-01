@extends('layouts.admin')

@section('title', 'Admin Airports | Zannora')
@section('page-title', 'Airports')

@section('content')
    <section class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-white/90">Kelola data bandara untuk rute penerbangan.</p>
            <a href="{{ route('admin.airports.create') }}" class="admin-btn-primary">Tambah Airport</a>
        </div>

        <form method="GET" class="admin-card grid gap-3 md:grid-cols-4">
            <div>
                <label class="admin-label" for="search">Search</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Code atau nama">
            </div>
            <div>
                <label class="admin-label" for="city">City</label>
                <input id="city" name="city" value="{{ $city }}" class="admin-field" placeholder="Kota">
            </div>
            <div>
                <label class="admin-label" for="country">Country</label>
                <input id="country" name="country" value="{{ $country }}" class="admin-field" placeholder="Negara">
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a class="admin-btn-secondary" href="{{ route('admin.airports.index') }}">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Flights Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($airports as $airport)
                            <tr>
                                <td class="font-semibold">{{ $airport->code }}</td>
                                <td>{{ $airport->name }}</td>
                                <td>{{ $airport->city }}</td>
                                <td>{{ $airport->country }}</td>
                                <td>{{ $airport->departure_flights_count + $airport->arrival_flights_count }}</td>
                                <td class="space-x-1">
                                    <a class="admin-btn-secondary" href="{{ route('admin.airports.show', $airport) }}">Detail</a>
                                    <a class="admin-btn-secondary" href="{{ route('admin.airports.edit', $airport) }}">Edit</a>
                                    <form action="{{ route('admin.airports.destroy', $airport) }}" method="POST" class="inline" onsubmit="return confirm('Hapus airport ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-btn-secondary" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-500">Data airport belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $airports->links() }}</div>
        </article>
    </section>
@endsection

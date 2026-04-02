@extends('layouts.admin')

@section('title', 'Admin Airplanes | Zannora')
@section('page-title', 'Airplanes')

@section('content')
    <section class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-white/90">Kelola pesawat milik maskapai.</p>
            <a href="{{ route('admin.airplanes.create') }}" class="admin-btn-primary">Tambah Airplane</a>
        </div>

        <form method="GET" class="admin-card grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="admin-label" for="search">Search Model / Registration</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Model atau registration number">
            </div>
            <div>
                <label class="admin-label" for="airline_id">Airline</label>
                <select id="airline_id" name="airline_id" class="admin-field">
                    <option value="">Semua airline</option>
                    @foreach ($airlines as $airline)
                        <option value="{{ $airline->id }}" @selected((int) $airlineId === (int) $airline->id)>{{ $airline->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a class="admin-btn-secondary" href="{{ route('admin.airplanes.index') }}">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Airline</th>
                            <th>Model</th>
                            <th>Registration</th>
                            <th>Capacity</th>
                            <th>Seats Count</th>
                            <th>Flights Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($airplanes as $airplane)
                            <tr>
                                <td>{{ $airplane->airline?->name }}</td>
                                <td class="font-semibold">{{ $airplane->model }}</td>
                                <td>{{ $airplane->registration_number }}</td>
                                <td>{{ $airplane->capacity }}</td>
                                <td>{{ $airplane->seats_count }}</td>
                                <td>{{ $airplane->flights_count }}</td>
                                <td class="space-x-1">
                                    <a class="admin-btn-secondary" href="{{ route('admin.airplanes.show', $airplane) }}">Detail</a>
                                    <a class="admin-btn-secondary" href="{{ route('admin.airplanes.edit', $airplane) }}">Edit</a>
                                    <form action="{{ route('admin.airplanes.destroy', $airplane) }}" method="POST" class="inline" onsubmit="return confirm('Hapus airplane ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-btn-secondary" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-slate-500">Data airplane belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $airplanes->links() }}</div>
        </article>
    </section>
@endsection

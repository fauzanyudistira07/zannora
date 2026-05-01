@extends('layouts.admin')

@section('title', 'Admin Airlines | Zannora')
@section('page-title', 'Airlines')

@section('content')
    <section class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-white/90">Kelola data maskapai.</p>
            <a href="{{ route('admin.airlines.create') }}" class="admin-btn-primary">Tambah Airline</a>
        </div>

        <form method="GET" class="admin-card grid gap-3 md:grid-cols-[1fr_auto]">
            <div>
                <label class="admin-label" for="search">Search Name / Code</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Nama atau kode airline">
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a href="{{ route('admin.airlines.index') }}" class="admin-btn-secondary">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Airplanes Count</th>
                            <th>Flights Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($airlines as $airline)
                            <tr>
                                <td>
                                    @if ($airline->logo)
                                        <img src="{{ str_starts_with($airline->logo, 'http') ? $airline->logo : asset('storage/'.$airline->logo) }}" alt="{{ $airline->name }}" class="h-8 w-8 rounded-full object-cover">
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="font-semibold">{{ $airline->code }}</td>
                                <td>{{ $airline->name }}</td>
                                <td>{{ $airline->airplanes_count }}</td>
                                <td>{{ $airline->flights_count }}</td>
                                <td class="space-x-1">
                                    <a class="admin-btn-secondary" href="{{ route('admin.airlines.show', $airline) }}">Detail</a>
                                    <a class="admin-btn-secondary" href="{{ route('admin.airlines.edit', $airline) }}">Edit</a>
                                    <form action="{{ route('admin.airlines.destroy', $airline) }}" method="POST" class="inline" onsubmit="return confirm('Hapus airline ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-btn-secondary" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-500">Data airline belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $airlines->links() }}</div>
        </article>
    </section>
@endsection

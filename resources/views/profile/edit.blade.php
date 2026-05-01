@extends('layouts.portal')

@section('title', 'Zannora | Profile')
@section('active', 'profile')

@section('content')
    <section class="grid gap-6 lg:grid-cols-2">
        <article class="portal-card">
            <h1 class="font-heading text-3xl font-bold text-[#0f3f78]">Profile</h1>
            <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="portal-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="portal-input" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="portal-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="portal-input" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="portal-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="portal-input">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="portal-btn-gold">Save Profile</button>
            </form>
        </article>

        <article class="portal-card">
            <h2 class="font-heading text-3xl font-bold text-[#0f3f78]">Change Password</h2>
            <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="portal-label">Current Password</label>
                    <input type="password" name="current_password" class="portal-input" required>
                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="portal-label">New Password</label>
                    <input type="password" name="password" class="portal-input" required>
                    @error('password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="portal-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="portal-input" required>
                </div>
                <button type="submit" class="portal-btn-gold">Update Password</button>
            </form>

            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-8 border-t border-slate-200 pt-5">
                @csrf
                @method('DELETE')
                <h3 class="text-lg font-semibold text-red-700">Delete Account</h3>
                <p class="mt-1 text-sm text-slate-600">This action is permanent. Please confirm your password.</p>
                <div class="mt-3">
                    <label class="portal-label">Password</label>
                    <input type="password" name="password" class="portal-input" required>
                    @error('password', 'userDeletion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="portal-btn-blue mt-3 border-red-200 bg-red-50 text-red-700 hover:bg-red-100">Delete Account</button>
            </form>
        </article>
    </section>
@endsection


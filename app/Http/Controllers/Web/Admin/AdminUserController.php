<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Passenger;
use App\Models\User;
use App\Support\UserRole;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('search'));

        $users = User::query()
            ->withCount(['passengers', 'bookings'])
            ->whereIn('role', UserRole::customerValues())
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function show(User $user)
    {
        $user->loadCount(['passengers', 'bookings']);

        $passengers = Passenger::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        $bookings = Booking::query()
            ->with(['flight.airline', 'payments', 'details.ticket'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        $paymentStats = [
            'paid' => $bookings->flatMap->payments->where('payment_status', 'paid')->count(),
            'pending' => $bookings->flatMap->payments->where('payment_status', 'pending')->count(),
            'failed' => $bookings->flatMap->payments->where('payment_status', 'failed')->count(),
        ];

        $ticketCount = $bookings
            ->flatMap->details
            ->filter(fn ($detail) => $detail->ticket !== null)
            ->count();

        return view('admin.users.show', compact('user', 'passengers', 'bookings', 'paymentStats', 'ticketCount'));
    }
}

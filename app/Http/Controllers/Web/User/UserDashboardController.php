<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()->bookings()
            ->with(['flight.airline'])
            ->latest()
            ->limit(5)
            ->get();

        return view('user.dashboard', [
            'stats' => [
                'active_bookings' => $request->user()->bookings()->whereIn('status', ['pending', 'confirmed'])->count(),
                'pending_payments' => $request->user()->bookings()->where('status', 'pending')->count(),
                'completed_trips' => $request->user()->bookings()->where('status', 'completed')->count(),
            ],
            'bookings' => $bookings,
        ]);
    }
}

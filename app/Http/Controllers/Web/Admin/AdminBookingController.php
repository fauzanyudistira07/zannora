<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status'));
        $date = trim((string) $request->string('date'));
        $user = trim((string) $request->string('user'));
        $flight = trim((string) $request->string('flight'));
        $paymentStatus = trim((string) $request->string('payment_status'));

        $bookings = Booking::query()
            ->with(['user', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport', 'payments'])
            ->when($search !== '', fn ($query) => $query->where('booking_code', 'like', "%{$search}%"))
            ->when(in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'], true), fn ($query) => $query->where('status', $status))
            ->when($date !== '', fn ($query) => $query->whereDate('created_at', $date))
            ->when($user !== '', fn ($query) => $query->whereHas('user', fn ($qq) => $qq->where('name', 'like', "%{$user}%")))
            ->when($flight !== '', fn ($query) => $query->whereHas('flight', fn ($qq) => $qq->where('flight_number', 'like', "%{$flight}%")))
            ->when(in_array($paymentStatus, ['pending', 'paid', 'failed', 'refunded'], true), fn ($query) => $query->whereHas('payments', fn ($qq) => $qq->where('payment_status', $paymentStatus)))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings', 'search', 'status', 'date', 'user', 'flight', 'paymentStatus'));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'user',
            'flight.airline',
            'flight.airplane',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'details.passenger',
            'details.seat',
            'details.ticket',
            'payments',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,completed'],
        ]);

        $booking->update(['status' => $data['status']]);

        return back()->with('status', 'Status booking berhasil diperbarui.');
    }

    public function cancel(Booking $booking)
    {
        $booking->update([
            'status' => 'cancelled',
            'expired_at' => now(),
        ]);

        $booking->payments()
            ->where('payment_status', 'pending')
            ->update(['payment_status' => 'failed']);

        return back()->with('status', 'Booking berhasil dibatalkan.');
    }
}

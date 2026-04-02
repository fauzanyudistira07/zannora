<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\AdminAnalyticsService;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected AdminAnalyticsService $analyticsService
    ) {
    }

    public function index()
    {
        $summary = $this->analyticsService->summary();

        $quickAlerts = [];
        if ($summary['payment_pending'] > 0) {
            $quickAlerts[] = "{$summary['payment_pending']} pembayaran menunggu verifikasi";
        }
        if ($summary['delayed_flights'] > 0) {
            $quickAlerts[] = "{$summary['delayed_flights']} flight berstatus delayed";
        }
        if ($summary['booking_expiring_today'] > 0) {
            $quickAlerts[] = "{$summary['booking_expiring_today']} booking akan expired hari ini";
        }

        return view('admin.dashboard', [
            'stats' => $summary,
            'recentBookings' => Booking::query()
                ->with(['user', 'flight.airline', 'flight.departureAirport', 'flight.arrivalAirport'])
                ->latest()
                ->limit(8)
                ->get(),
            'recentPayments' => Payment::query()
                ->with(['booking.user', 'booking.flight'])
                ->latest()
                ->limit(8)
                ->get(),
            'bookingChart' => $this->analyticsService->monthlyBookingChart(6),
            'paymentStatusChart' => $this->analyticsService->paymentStatusBreakdown(),
            'quickAlerts' => $quickAlerts,
        ]);
    }
}

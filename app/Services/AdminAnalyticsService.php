<?php

namespace App\Services;

use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsService
{
    public function summary(): array
    {
        $now = now();

        return [
            'total_users' => User::query()->where('role', 'user')->count(),
            'total_passengers' => Passenger::query()->count(),
            'total_airports' => Airport::query()->count(),
            'total_airlines' => Airline::query()->count(),
            'total_airplanes' => Airplane::query()->count(),
            'total_flights' => Flight::query()->count(),
            'total_bookings' => Booking::query()->count(),
            'total_payments' => Payment::query()->count(),
            'total_tickets' => Ticket::query()->count(),
            'booking_pending' => Booking::query()->where('status', 'pending')->count(),
            'booking_confirmed' => Booking::query()->where('status', 'confirmed')->count(),
            'booking_cancelled' => Booking::query()->where('status', 'cancelled')->count(),
            'payment_pending' => Payment::query()->where('payment_status', 'pending')->count(),
            'revenue_total' => (float) Payment::query()->where('payment_status', 'paid')->sum('amount'),
            'revenue_this_month' => (float) Payment::query()
                ->where('payment_status', 'paid')
                ->whereYear('paid_at', $now->year)
                ->whereMonth('paid_at', $now->month)
                ->sum('amount'),
            'delayed_flights' => Flight::query()->where('status', 'delayed')->count(),
            'booking_expiring_today' => Booking::query()
                ->where('status', 'pending')
                ->whereDate('expired_at', $now->toDateString())
                ->count(),
        ];
    }

    /**
     * @return Collection<int, array{month:string,total:int}>
     */
    public function monthlyBookingChart(int $months = 6): Collection
    {
        $start = now()->startOfMonth()->subMonths($months - 1);

        $data = Booking::query()
            ->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->groupBy(fn (Booking $booking) => optional($booking->created_at)->format('Y-m'))
            ->map(fn (Collection $items) => $items->count());

        return collect(range(0, $months - 1))->map(function (int $offset) use ($start, $data) {
            $month = $start->copy()->addMonths($offset);
            $key = $month->format('Y-m');

            return [
                'month' => $month->format('M Y'),
                'total' => (int) ($data[$key] ?? 0),
            ];
        });
    }

    public function paymentStatusBreakdown(): array
    {
        return [
            'paid' => Payment::query()->where('payment_status', 'paid')->count(),
            'pending' => Payment::query()->where('payment_status', 'pending')->count(),
        ];
    }

    public function reportSummary(array $filters = []): array
    {
        $bookingQuery = Booking::query()
            ->with(['flight.departureAirport', 'flight.arrivalAirport', 'flight.airline']);
        $paymentQuery = Payment::query()->with(['booking.flight.departureAirport', 'booking.flight.arrivalAirport']);
        $flightQuery = Flight::query();

        if (! empty($filters['date_from'])) {
            $bookingQuery->whereDate('created_at', '>=', $filters['date_from']);
            $paymentQuery->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $bookingQuery->whereDate('created_at', '<=', $filters['date_to']);
            $paymentQuery->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['airline_id'])) {
            $bookingQuery->whereHas('flight', fn ($q) => $q->where('airline_id', $filters['airline_id']));
            $paymentQuery->whereHas('booking.flight', fn ($q) => $q->where('airline_id', $filters['airline_id']));
            $flightQuery->where('airline_id', $filters['airline_id']);
        }

        if (! empty($filters['booking_status'])) {
            $bookingQuery->where('status', $filters['booking_status']);
        }

        if (! empty($filters['payment_status'])) {
            $paymentQuery->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['route'])) {
            $route = trim((string) $filters['route']);
            $bookingQuery->whereHas('flight', function ($q) use ($route) {
                $q->whereHas('departureAirport', fn ($qq) => $qq->where('code', 'like', "%{$route}%"))
                    ->orWhereHas('arrivalAirport', fn ($qq) => $qq->where('code', 'like', "%{$route}%"));
            });
        }

        $paidPaymentQuery = (clone $paymentQuery)->where('payment_status', 'paid');

        $popularRoutes = (clone $bookingQuery)
            ->select('flight_id', DB::raw('COUNT(*) as total'))
            ->groupBy('flight_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function (Booking $booking) {
                $flight = $booking->flight;

                return [
                    'flight_id' => $flight?->id,
                    'route' => $flight
                        ? sprintf('%s -> %s', $flight->departureAirport?->code, $flight->arrivalAirport?->code)
                        : '-',
                    'flight_number' => $flight?->flight_number,
                    'total_bookings' => (int) $booking->total,
                ];
            })
            ->values();

        $monthlyRevenue = (clone $paidPaymentQuery)
            ->whereNotNull('paid_at')
            ->get(['paid_at', 'amount'])
            ->groupBy(fn (Payment $payment) => optional($payment->paid_at)->format('Y-m'))
            ->map(fn (Collection $items, string $month) => [
                'month' => $month,
                'total' => (float) $items->sum('amount'),
            ])
            ->sortBy('month')
            ->values();

        return [
            'total_bookings' => $bookingQuery->count(),
            'total_paid_payments' => $paidPaymentQuery->count(),
            'total_cancelled_bookings' => (clone $bookingQuery)->where('status', 'cancelled')->count(),
            'total_active_flights' => $flightQuery->whereIn('status', ['scheduled', 'delayed'])->count(),
            'revenue_total' => (float) $paidPaymentQuery->sum('amount'),
            'popular_routes' => $popularRoutes,
            'monthly_revenue' => $monthlyRevenue,
        ];
    }
}

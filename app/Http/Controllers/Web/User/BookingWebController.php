<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Flight;
use App\Services\BookingService;
use App\Services\FlightService;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingWebController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected FlightService $flightService,
        protected PaymentService $paymentService
    ) {
    }

    public function index(Request $request): View
    {
        $bookings = $request->user()->bookings()
            ->with([
                'flight.airline',
                'flight.departureAirport',
                'flight.arrivalAirport',
                'details.passenger',
                'details.seat',
                'payments',
            ])
            ->latest()
            ->paginate(10);

        $bookings->setCollection(
            $bookings->getCollection()->map(fn (Booking $booking) => $this->syncPendingMidtransPayment($booking))
        );

        return view('user.bookings.index', [
            'bookings' => $bookings,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $flightId = $request->integer('flight');
        $flight = Flight::query()
            ->with(['airline', 'airplane.seats', 'departureAirport', 'arrivalAirport'])
            ->find($flightId);

        if (! $flight) {
            return redirect()
                ->route('flights.index')
                ->with('status', 'Pilih flight terlebih dahulu sebelum booking.');
        }

        $availableSeats = $this->flightService->availableSeats($flight);
        $bookedSeatIds = BookingDetail::query()
            ->whereHas('booking', function ($query) use ($flight) {
                $query->where('flight_id', $flight->id)
                    ->seatLocking();
            })
            ->pluck('seat_id');

        $passengers = $request->user()
            ->passengers()
            ->orderBy('full_name')
            ->get();

        return view('user.booking.create', [
            'flight' => $flight,
            'passengers' => $passengers,
            'availableSeats' => $availableSeats,
            'bookedSeatIds' => $bookedSeatIds,
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $booking = $this->bookingService->createBooking($request->user(), $request->validated());

        return redirect()
            ->route('payments.create', ['booking' => $booking->id])
            ->with('status', 'Booking berhasil dibuat dengan kode '.$booking->booking_code);
    }

    public function show(Request $request, Booking $booking): View
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        $booking->load([
            'flight.airline',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'details.passenger',
            'details.seat',
            'details.ticket',
            'payments',
        ]);

        $booking = $this->syncPendingMidtransPayment($booking);

        return view('user.bookings.show', [
            'booking' => $booking,
            'latestPayment' => $booking->payments->sortByDesc('created_at')->first(),
        ]);
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->bookingService->cancelBooking($request->user(), $booking);

        return redirect()
            ->route('my-bookings.show', $booking)
            ->with('status', 'Booking berhasil dibatalkan.');
    }

    protected function syncPendingMidtransPayment(Booking $booking): Booking
    {
        $latestPayment = $booking->payments->sortByDesc('created_at')->first();

        if (! $latestPayment || $latestPayment->payment_method !== 'midtrans_snap' || $latestPayment->payment_status !== 'pending') {
            return $booking;
        }

        try {
            $this->paymentService->syncFromMidtransGateway($latestPayment, 3, 500);
        } catch (\Throwable) {
            // Keep current booking state if Midtrans status check fails.
        }

        $booking->refresh();
        $booking->load([
            'flight.airline',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'details.passenger',
            'details.seat',
            'details.ticket',
            'payments',
        ]);

        return $booking;
    }
}

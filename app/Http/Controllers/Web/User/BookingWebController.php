<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Flight;
use App\Services\BookingService;
use App\Services\FlightService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingWebController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected FlightService $flightService
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
                    ->whereIn('status', ['pending', 'confirmed', 'completed']);
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
}

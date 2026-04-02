<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminFlightController extends Controller
{
    public function index(Request $request)
    {
        $date = trim((string) $request->string('date'));
        $route = trim((string) $request->string('route'));
        $airlineId = $request->integer('airline_id');
        $status = trim((string) $request->string('status'));
        $search = trim((string) $request->string('search'));

        $flights = Flight::query()
            ->with(['airline', 'airplane', 'departureAirport', 'arrivalAirport'])
            ->withCount('bookings')
            ->withCount([
                'bookings as booked_seats' => fn ($query) => $query->selectRaw('coalesce(sum(total_passengers),0)'),
            ])
            ->when($search !== '', fn ($query) => $query->where('flight_number', 'like', "%{$search}%"))
            ->when($date !== '', fn ($query) => $query->whereDate('departure_time', $date))
            ->when($airlineId > 0, fn ($query) => $query->where('airline_id', $airlineId))
            ->when(in_array($status, ['scheduled', 'delayed', 'cancelled', 'completed'], true), fn ($query) => $query->where('status', $status))
            ->when($route !== '', function ($query) use ($route) {
                $query->where(function ($qq) use ($route) {
                    $qq->whereHas('departureAirport', fn ($q) => $q->where('code', 'like', "%{$route}%"))
                        ->orWhereHas('arrivalAirport', fn ($q) => $q->where('code', 'like', "%{$route}%"));
                });
            })
            ->orderByDesc('departure_time')
            ->paginate(12)
            ->withQueryString();

        $airlines = Airline::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.flights.index', compact('flights', 'airlines', 'date', 'route', 'airlineId', 'status', 'search'));
    }

    public function create()
    {
        $airlines = Airline::query()->orderBy('name')->get();
        $airplanes = Airplane::query()->with('airline')->orderBy('model')->get();
        $airports = Airport::query()->orderBy('code')->get();

        return view('admin.flights.create', compact('airlines', 'airplanes', 'airports'));
    }

    public function store(Request $request)
    {
        $data = $this->validateFlight($request);

        Flight::create($data);

        return redirect()->route('admin.flights.index')->with('status', 'Flight berhasil dibuat.');
    }

    public function show(Flight $flight)
    {
        $flight->load([
            'airline',
            'airplane.seats',
            'departureAirport',
            'arrivalAirport',
            'bookings.user',
            'bookings.details.passenger',
            'bookings.details.seat',
            'bookings.payments',
            'bookings.details.ticket',
        ]);

        $bookedSeatIds = $flight->bookings
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->flatMap->details
            ->pluck('seat_id')
            ->filter()
            ->unique()
            ->values();

        $totalSeats = $flight->airplane?->seats?->count() ?? 0;
        $availableSeats = max($totalSeats - $bookedSeatIds->count(), 0);
        $paymentSummary = [
            'paid' => $flight->bookings->flatMap->payments->where('payment_status', 'paid')->count(),
            'pending' => $flight->bookings->flatMap->payments->where('payment_status', 'pending')->count(),
            'failed' => $flight->bookings->flatMap->payments->where('payment_status', 'failed')->count(),
        ];

        return view('admin.flights.show', compact('flight', 'availableSeats', 'paymentSummary', 'bookedSeatIds'));
    }

    public function edit(Flight $flight)
    {
        $airlines = Airline::query()->orderBy('name')->get();
        $airplanes = Airplane::query()->with('airline')->orderBy('model')->get();
        $airports = Airport::query()->orderBy('code')->get();

        return view('admin.flights.edit', compact('flight', 'airlines', 'airplanes', 'airports'));
    }

    public function update(Request $request, Flight $flight)
    {
        $data = $this->validateFlight($request, $flight);

        $flight->update($data);

        return redirect()->route('admin.flights.index')->with('status', 'Flight berhasil diperbarui.');
    }

    public function destroy(Flight $flight)
    {
        if ($flight->bookings()->exists()) {
            return back()->withErrors(['flight' => 'Flight tidak dapat dihapus karena sudah memiliki booking.']);
        }

        $flight->delete();

        return redirect()->route('admin.flights.index')->with('status', 'Flight berhasil dihapus.');
    }

    public function updateStatus(Request $request, Flight $flight)
    {
        $data = $request->validate([
            'status' => ['required', 'in:scheduled,delayed,cancelled,completed'],
        ]);

        $flight->update(['status' => $data['status']]);

        return back()->with('status', 'Status flight berhasil diperbarui.');
    }

    protected function validateFlight(Request $request, ?Flight $flight = null): array
    {
        $data = $request->validate([
            'airline_id' => ['required', 'exists:airlines,id'],
            'airplane_id' => ['required', 'exists:airplanes,id'],
            'departure_airport_id' => ['required', 'exists:airports,id', 'different:arrival_airport_id'],
            'arrival_airport_id' => ['required', 'exists:airports,id'],
            'flight_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('flights', 'flight_number')->ignore($flight?->id),
            ],
            'departure_time' => ['required', 'date'],
            'arrival_time' => ['required', 'date', 'after:departure_time'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:scheduled,delayed,cancelled,completed'],
        ]);

        $airplane = Airplane::query()->find($data['airplane_id']);
        if ($airplane && (int) $airplane->airline_id !== (int) $data['airline_id']) {
            throw ValidationException::withMessages([
                'airplane_id' => ['Airplane harus sesuai dengan airline terpilih.'],
            ]);
        }

        return $data;
    }
}

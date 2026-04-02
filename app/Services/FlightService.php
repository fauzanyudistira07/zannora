<?php

namespace App\Services;

use App\Models\BookingDetail;
use App\Models\Flight;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FlightService
{
    public function search(array $filters = []): Builder
    {
        $departureDate = $filters['departure_date'] ?? $filters['date'] ?? null;

        return Flight::query()
            ->with(['airline', 'airplane', 'departureAirport', 'arrivalAirport'])
            ->when($filters['departure_airport_id'] ?? null, fn (Builder $query, $id) => $query->where('departure_airport_id', $id))
            ->when($filters['arrival_airport_id'] ?? null, fn (Builder $query, $id) => $query->where('arrival_airport_id', $id))
            ->when($departureDate, fn (Builder $query, $date) => $query->whereDate('departure_time', $date))
            ->when($filters['passengers'] ?? null, function (Builder $query, $passengers) {
                $count = max((int) $passengers, 1);

                $query->whereHas('airplane', fn (Builder $airplane) => $airplane->where('capacity', '>=', $count));
            })
            ->when($filters['class'] ?? null, function (Builder $query, $class) {
                $query->whereHas('airplane.seats', fn (Builder $seat) => $seat->where('class', $class));
            })
            ->when($filters['airline_id'] ?? null, fn (Builder $query, $id) => $query->where('airline_id', $id))
            ->when($filters['status'] ?? null, fn (Builder $query, $status) => $query->where('status', $status))
            ->orderBy('departure_time');
    }

    public function availableSeats(Flight $flight): Collection
    {
        $bookedSeatIds = BookingDetail::query()
            ->whereHas('booking', function (Builder $query) use ($flight) {
                $query->where('flight_id', $flight->id)
                    ->whereIn('status', ['pending', 'confirmed', 'completed']);
            })
            ->pluck('seat_id');

        return Seat::query()
            ->where('airplane_id', $flight->airplane_id)
            ->whereNotIn('id', $bookedSeatIds)
            ->orderBy('seat_number')
            ->get();
    }
}

<?php

namespace App\Services;

use App\Models\Airplane;
use App\Models\Seat;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SeatGeneratorService
{
    /**
     * @return Collection<int, Seat>
     */
    public function generateForAirplane(Airplane $airplane, string $class = 'economy', bool $reset = false): Collection
    {
        return DB::transaction(function () use ($airplane, $class, $reset) {
            if (! in_array($class, ['economy', 'business', 'first'], true)) {
                throw ValidationException::withMessages([
                    'class' => ['Class seat tidak valid.'],
                ]);
            }

            if ($reset) {
                $airplane->seats()->delete();
            }

            $existingSeatCount = $airplane->seats()->count();
            $remaining = max($airplane->capacity - $existingSeatCount, 0);

            if ($remaining === 0) {
                return $airplane->seats()->orderBy('seat_number')->get();
            }

            $generatedSeats = [];
            $row = intdiv($existingSeatCount, 6) + 1;
            $colIndex = $existingSeatCount % 6;
            $seatColumns = ['A', 'B', 'C', 'D', 'E', 'F'];

            for ($i = 0; $i < $remaining; $i++) {
                $seatNumber = $row.$seatColumns[$colIndex];

                while ($airplane->seats()->where('seat_number', $seatNumber)->exists()) {
                    $colIndex++;
                    if ($colIndex >= 6) {
                        $colIndex = 0;
                        $row++;
                    }

                    $seatNumber = $row.$seatColumns[$colIndex];
                }

                $generatedSeats[] = [
                    'airplane_id' => $airplane->id,
                    'seat_number' => $seatNumber,
                    'class' => $class,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $colIndex++;
                if ($colIndex >= 6) {
                    $colIndex = 0;
                    $row++;
                }
            }

            if ($generatedSeats !== []) {
                Seat::query()->insert($generatedSeats);
            }

            return $airplane->seats()->orderBy('seat_number')->get();
        });
    }
}

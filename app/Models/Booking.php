<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'flight_id',
        'booking_code',
        'total_passengers',
        'total_price',
        'status',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'expired_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeSeatLocking(Builder $query): Builder
    {
        return $query->where(function (Builder $seatLockQuery) {
            $seatLockQuery
                ->whereIn('status', ['confirmed', 'completed'])
                ->orWhere(function (Builder $pendingQuery) {
                    $pendingQuery
                        ->where('status', 'pending')
                        ->where(function (Builder $paymentQuery) {
                            $paymentQuery
                                ->whereDoesntHave('payments')
                                ->orWhereHas('payments', fn (Builder $q) => $q->where('payment_status', 'pending'));
                        });
                });
        });
    }

    public function locksSeat(): bool
    {
        if (in_array($this->status, ['confirmed', 'completed'], true)) {
            return true;
        }

        if ($this->status !== 'pending') {
            return false;
        }

        $latestPayment = $this->relationLoaded('payments')
            ? $this->payments->sortByDesc('id')->first()
            : $this->payments()->latest('id')->first();

        if (! $latestPayment) {
            return true;
        }

        return $latestPayment->payment_status === 'pending';
    }
}

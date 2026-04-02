<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Ticket;

class TicketService
{
    public function issueForBooking(Booking $booking): Booking
    {
        $booking->loadMissing('details.ticket');

        foreach ($booking->details as $detail) {
            if (! $detail->ticket_number) {
                $detail->update([
                    'ticket_number' => $this->generateTicketNumber($booking->booking_code, $detail->id),
                ]);
            }

            Ticket::updateOrCreate(
                ['booking_detail_id' => $detail->id],
                ['issued_at' => now()]
            );
        }

        return $booking->fresh(['details.ticket']);
    }

    public function generateTicketNumber(string $bookingCode, int $detailId): string
    {
        return 'TK-' . preg_replace('/[^A-Z0-9]/', '', strtoupper($bookingCode)) . '-' . str_pad((string) $detailId, 4, '0', STR_PAD_LEFT);
    }
}

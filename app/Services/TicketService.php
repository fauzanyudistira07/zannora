<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketService
{
    public function issueForBooking(Booking $booking): Booking
    {
        $booking->loadMissing([
            'flight.departureAirport',
            'flight.arrivalAirport',
            'details.ticket',
            'details.passenger',
            'details.seat',
        ]);

        foreach ($booking->details as $detail) {
            if (! $detail->ticket_number) {
                $detail->update([
                    'ticket_number' => $this->generateTicketNumber($booking->booking_code, $detail->id),
                ]);
            }

            $ticket = Ticket::updateOrCreate(
                ['booking_detail_id' => $detail->id],
                ['issued_at' => now()]
            );

            if (! $ticket->qr_code_path) {
                $qrPath = $this->generateQrCodeForTicket($ticket, $booking, $detail);
                if ($qrPath !== null) {
                    $ticket->update(['qr_code_path' => $qrPath]);
                }
            }
        }

        return $booking->fresh(['details.ticket']);
    }

    public function generateTicketNumber(string $bookingCode, int $detailId): string
    {
        return 'TK-' . preg_replace('/[^A-Z0-9]/', '', strtoupper($bookingCode)) . '-' . str_pad((string) $detailId, 4, '0', STR_PAD_LEFT);
    }

    protected function generateQrCodeForTicket(Ticket $ticket, Booking $booking, $detail): ?string
    {
        $ticketNumber = (string) ($detail->ticket_number ?: ('TICKET-'.$detail->id));
        $route = ($booking->flight?->departureAirport?->code ?? '-').'->'.($booking->flight?->arrivalAirport?->code ?? '-');
        $passengerName = (string) ($detail->passenger?->full_name ?? 'Passenger');
        $seat = (string) ($detail->seat?->seat_number ?? '-');
        $departure = optional($booking->flight?->departure_time)->format('Y-m-d H:i') ?: '-';

        $payload = implode("\n", [
            'ZANNORA E-TICKET',
            'Ticket: '.$ticketNumber,
            'Booking: '.$booking->booking_code,
            'Passenger: '.$passengerName,
            'Seat: '.$seat,
            'Route: '.$route,
            'Departure: '.$departure,
        ]);

        $qrUrls = [
            'https://api.qrserver.com/v1/create-qr-code/?size=420x420&format=png&data='.urlencode($payload),
            'https://quickchart.io/qr?size=420&text='.urlencode($payload),
        ];

        try {
            $responseBody = null;

            foreach ($qrUrls as $qrUrl) {
                $response = Http::withOptions(['verify' => false])->timeout(15)->get($qrUrl);
                if ($response->successful() && $response->body() !== '') {
                    $responseBody = $response->body();
                    break;
                }
            }

            if ($responseBody === null) {
                return null;
            }

            $safeName = Str::slug($ticketNumber, '-');
            $relativePath = 'tickets/qr/'.$safeName.'.png';
            Storage::disk('public')->put($relativePath, $responseBody);

            return $relativePath;
        } catch (\Throwable) {
            return null;
        }
    }
}

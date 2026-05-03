<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TicketWebController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {
    }

    public function show(Request $request, Ticket $ticket): View
    {
        $ticket->load([
            'bookingDetail.booking.user',
            'bookingDetail.booking.flight.airline',
            'bookingDetail.booking.flight.departureAirport',
            'bookingDetail.booking.flight.arrivalAirport',
            'bookingDetail.passenger',
            'bookingDetail.seat',
        ]);

        abort_unless($ticket->bookingDetail->booking->user_id === $request->user()->id, 403);

        if (! $ticket->qr_code_path) {
            $this->ticketService->issueForBooking($ticket->bookingDetail->booking);
            $ticket->refresh();
            $ticket->load([
                'bookingDetail.booking.user',
                'bookingDetail.booking.flight.airline',
                'bookingDetail.booking.flight.departureAirport',
                'bookingDetail.booking.flight.arrivalAirport',
                'bookingDetail.passenger',
                'bookingDetail.seat',
            ]);
        }

        return view('user.tickets.show', [
            'ticket' => $ticket,
            'detail' => $ticket->bookingDetail,
            'booking' => $ticket->bookingDetail->booking,
            'flight' => $ticket->bookingDetail->booking->flight,
        ]);
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'total_passengers' => $this->total_passengers,
            'total_price' => (float) $this->total_price,
            'status' => $this->status,
            'expired_at' => optional($this->expired_at)->toDateTimeString(),
            'flight' => $this->whenLoaded('flight', fn () => [
                'id' => $this->flight->id,
                'flight_number' => $this->flight->flight_number,
                'departure_time' => optional($this->flight->departure_time)->toDateTimeString(),
                'arrival_time' => optional($this->flight->arrival_time)->toDateTimeString(),
            ]),
            'details' => $this->whenLoaded('details', fn () => $this->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'price' => (float) $detail->price,
                    'ticket_number' => $detail->ticket_number,
                    'boarding_status' => $detail->boarding_status,
                    'passenger' => $detail->passenger ? [
                        'id' => $detail->passenger->id,
                        'full_name' => $detail->passenger->full_name,
                    ] : null,
                    'seat' => $detail->seat ? [
                        'id' => $detail->seat->id,
                        'seat_number' => $detail->seat->seat_number,
                        'class' => $detail->seat->class,
                    ] : null,
                    'ticket' => $detail->ticket ? [
                        'id' => $detail->ticket->id,
                        'qr_code_path' => $detail->ticket->qr_code_path,
                        'pdf_path' => $detail->ticket->pdf_path,
                        'issued_at' => optional($detail->ticket->issued_at)->toDateTimeString(),
                    ] : null,
                ];
            })->values()),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}

<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class NotificationWebController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $request->user()->bookings()
            ->with(['payments'])
            ->latest()
            ->limit(8)
            ->get();

        $notifications = $bookings->flatMap(function ($booking) {
            $items = [
                [
                    'title' => 'Booking '.$booking->booking_code,
                    'description' => 'Status booking Anda saat ini: '.ucfirst($booking->status).'.',
                    'time' => $booking->updated_at,
                    'type' => 'booking',
                ],
            ];

            $latestPayment = $booking->payments->sortByDesc('created_at')->first();
            if ($latestPayment) {
                $items[] = [
                    'title' => 'Pembayaran '.$booking->booking_code,
                    'description' => 'Status pembayaran: '.ucfirst($latestPayment->payment_status).'.',
                    'time' => $latestPayment->updated_at,
                    'type' => 'payment',
                ];
            }

            return $items;
        })->sortByDesc('time')->values();

        return view('user.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}


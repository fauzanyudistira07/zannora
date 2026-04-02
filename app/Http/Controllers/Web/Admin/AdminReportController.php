<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Services\AdminAnalyticsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function __construct(
        protected AdminAnalyticsService $analyticsService
    ) {
    }

    public function index(Request $request)
    {
        $filters = [
            'date_from' => $request->string('date_from')->toString(),
            'date_to' => $request->string('date_to')->toString(),
            'airline_id' => $request->integer('airline_id'),
            'route' => $request->string('route')->toString(),
            'payment_status' => $request->string('payment_status')->toString(),
            'booking_status' => $request->string('booking_status')->toString(),
        ];

        $summary = $this->analyticsService->reportSummary($filters);
        $airlines = Airline::query()->orderBy('name')->get(['id', 'name']);

        if ($request->string('export')->toString() === 'csv') {
            return $this->exportCsv($summary);
        }

        return view('admin.reports.index', compact('summary', 'filters', 'airlines'));
    }

    protected function exportCsv(array $summary): StreamedResponse
    {
        $filename = 'admin-report-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($summary) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Total Bookings', $summary['total_bookings']]);
            fputcsv($handle, ['Total Paid Payments', $summary['total_paid_payments']]);
            fputcsv($handle, ['Total Cancelled Bookings', $summary['total_cancelled_bookings']]);
            fputcsv($handle, ['Total Active Flights', $summary['total_active_flights']]);
            fputcsv($handle, ['Revenue Total', $summary['revenue_total']]);
            fputcsv($handle, []);
            fputcsv($handle, ['Popular Route', 'Flight Number', 'Total Bookings']);

            foreach ($summary['popular_routes'] as $route) {
                fputcsv($handle, [
                    $route['route'],
                    $route['flight_number'],
                    $route['total_bookings'],
                ]);
            }

            fclose($handle);
        }, $filename);
    }
}

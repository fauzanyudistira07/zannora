<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Admin\AdminAirlineController;
use App\Http\Controllers\Web\Admin\AdminAirplaneController;
use App\Http\Controllers\Web\Admin\AdminAirportController;
use App\Http\Controllers\Web\Admin\AdminBookingController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminFlightController;
use App\Http\Controllers\Web\Admin\AdminPassengerController as AdminPassengerWebController;
use App\Http\Controllers\Web\Admin\AdminPaymentController;
use App\Http\Controllers\Web\Admin\AdminProfileController;
use App\Http\Controllers\Web\Admin\AdminReportController;
use App\Http\Controllers\Web\Admin\AdminSeatController;
use App\Http\Controllers\Web\Admin\AdminTicketController;
use App\Http\Controllers\Web\Admin\AdminUserController;
use App\Http\Controllers\Web\User\BookingWebController;
use App\Http\Controllers\Web\User\NotificationWebController;
use App\Http\Controllers\Web\User\PassengerWebController;
use App\Http\Controllers\Web\User\PaymentWebController;
use App\Http\Controllers\Web\User\FlightSearchController;
use App\Http\Controllers\Web\User\TicketWebController;
use App\Http\Controllers\Web\User\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FlightSearchController::class, 'index'])->name('home');
Route::get('/home', [FlightSearchController::class, 'index'])->name('home.index');
Route::get('/flights', [FlightSearchController::class, 'results'])->name('flights.index');
Route::get('/flights/search', [FlightSearchController::class, 'search'])->name('flights.search');
Route::get('/flights/{flight}', [FlightSearchController::class, 'show'])->name('flights.show');

Route::get('/dashboard', function () {
    return auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::middleware('role:user')->group(function () {
        Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::get('/booking', [BookingWebController::class, 'create'])->name('booking.create');
        Route::post('/booking', [BookingWebController::class, 'store'])->name('booking.store');
        Route::post('/bookings', [BookingWebController::class, 'store'])->name('bookings.store');
        Route::get('/my-bookings', [BookingWebController::class, 'index'])->name('my-bookings.index');
        Route::get('/my-bookings/{booking}', [BookingWebController::class, 'show'])->name('my-bookings.show');
        Route::post('/my-bookings/{booking}/cancel', [BookingWebController::class, 'cancel'])->name('my-bookings.cancel');

        Route::get('/payment', [PaymentWebController::class, 'create'])->name('payments.create');
        Route::post('/payment', [PaymentWebController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentWebController::class, 'show'])->name('payments.show');

        Route::get('/passengers', [PassengerWebController::class, 'index'])->name('passengers.index');
        Route::post('/passengers', [PassengerWebController::class, 'store'])->name('passengers.store');
        Route::put('/passengers/{passenger}', [PassengerWebController::class, 'update'])->name('passengers.update');
        Route::delete('/passengers/{passenger}', [PassengerWebController::class, 'destroy'])->name('passengers.destroy');

        Route::get('/tickets/{ticket}', [TicketWebController::class, 'show'])->name('tickets.show');
        Route::get('/notifications', [NotificationWebController::class, 'index'])->name('notifications.index');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');

        Route::get('/passengers', [AdminPassengerWebController::class, 'index'])->name('passengers.index');
        Route::get('/passengers/{passenger}', [AdminPassengerWebController::class, 'show'])->name('passengers.show');

        Route::resource('airports', AdminAirportController::class);
        Route::resource('airlines', AdminAirlineController::class);
        Route::resource('airplanes', AdminAirplaneController::class);
        Route::post('/airplanes/{airplane}/generate-seats', [AdminAirplaneController::class, 'generateSeats'])->name('airplanes.generate-seats');

        Route::resource('seats', AdminSeatController::class);

        Route::resource('flights', AdminFlightController::class);
        Route::patch('/flights/{flight}/status', [AdminFlightController::class, 'updateStatus'])->name('flights.status');

        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
        Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');

        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

        Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{ticket}/regenerate', [AdminTicketController::class, 'regenerate'])->name('tickets.regenerate');

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

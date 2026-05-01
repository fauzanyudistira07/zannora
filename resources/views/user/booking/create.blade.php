@extends('layouts.portal')

@section('title', 'Zannora | Booking')
@section('active', 'flights')

@section('content')
    @php
        $classKeys = $seatMap['class_keys'];
        $initialClass = in_array($selectedClass, $classKeys, true) ? $selectedClass : ($classKeys[0] ?? 'economy');
        $seatCatalog = $flight->airplane->seats
            ->map(fn ($seat) => ['id' => $seat->id, 'seat_number' => $seat->seat_number, 'class' => $seat->class])
            ->values();
    @endphp

    <section x-data="bookingWizard(
        {{ Js::from($passengers->map(fn ($p) => ['id' => $p->id, 'name' => $p->full_name])->values()) }},
        {{ Js::from($seatCatalog) }},
        {{ Js::from($seatMap['available_ids']) }},
        {{ Js::from($classPrices) }},
        '{{ $initialClass }}'
    )" class="space-y-6">
        <article class="portal-card">
            <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $flight->airline->name }}</p>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="portal-fluid-title mt-1 font-heading font-bold text-slate-800">Booking {{ $flight->flight_number }}</h1>
                    <p class="mt-2 text-slate-600">
                        {{ $flight->departureAirport->city }} ({{ $flight->departureAirport->code }})
                        → {{ $flight->arrivalAirport->city }} ({{ $flight->arrivalAirport->code }})
                    </p>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $flight->departure_time->format('d M Y H:i') }} - {{ $flight->arrival_time->format('d M Y H:i') }}
                    </p>
                </div>
                <div class="grid gap-2 sm:text-right">
                    <span class="portal-status-default">Base fare Rp{{ number_format((float) $flight->price, 0, ',', '.') }}</span>
                    <span class="text-sm font-semibold text-slate-600">Cabin price updates automatically</span>
                </div>
            </div>
        </article>

        <article class="portal-card">
            <div class="flex flex-wrap items-center gap-3">
                <span :class="step === 1 ? 'portal-status-confirmed' : 'portal-status-default'">Step 1 - Passenger</span>
                <span :class="step === 2 ? 'portal-status-confirmed' : 'portal-status-default'">Step 2 - Cabin & Seat</span>
                <span :class="step === 3 ? 'portal-status-confirmed' : 'portal-status-default'">Step 3 - Summary</span>
            </div>

            <form method="POST" action="{{ route('booking.store') }}" class="mt-6" @submit.prevent="submitForm($event)">
                @csrf
                <input type="hidden" name="flight_id" value="{{ $flight->id }}">
                <input type="hidden" name="seat_class" :value="selectedClass">
                <div x-ref="hiddenInputs"></div>

                <div x-show="step === 1" x-cloak>
                    <h2 class="font-heading text-xl font-bold text-[#0f3f78] sm:text-2xl">Pilih Passenger</h2>
                    <p class="mt-1 text-sm text-slate-600">Pilih minimal satu passenger untuk booking ini.</p>
                    @if ($passengers->isEmpty())
                        <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-white/70 p-5 text-slate-600">
                            Belum ada passenger. Tambahkan passenger dulu.
                            <div class="mt-4">
                                <a href="{{ route('passengers.index') }}" class="portal-btn-blue">Tambah Passenger</a>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($passengers as $passenger)
                                <label class="portal-card-soft flex cursor-pointer items-center justify-between">
                                    <span>
                                        <span class="block font-semibold text-slate-800">{{ $passenger->full_name }}</span>
                                        <span class="text-sm text-slate-500">{{ optional($passenger->birth_date)->format('d M Y') }}</span>
                                    </span>
                                    <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#0f3f78]" :value="{{ $passenger->id }}" x-model="selectedPassengers">
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div x-show="step === 2" x-cloak class="space-y-5">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-[#0f3f78] sm:text-2xl">Pilih Class & Seat</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Layout kursi berubah mengikuti class kabin yang dipilih. Pilih seat sesuai jumlah passenger.
                        </p>
                    </div>

                    <div class="grid gap-3 lg:grid-cols-3">
                        @foreach ($seatMap['classes'] as $classKey => $seatClass)
                            <button
                                type="button"
                                @click="setClass('{{ $classKey }}')"
                                class="portal-card-soft text-left transition duration-200"
                                :class="selectedClass === '{{ $classKey }}' ? 'ring-2 ring-[#0f3f78] border-[#0f3f78] bg-white' : 'hover:bg-white'"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-lg font-semibold text-slate-800">{{ $seatClass['label'] }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $seatClass['description'] }}</p>
                                    </div>
                                    <span class="portal-status-default">{{ $availableSeatCounts[$classKey] ?? 0 }} seats</span>
                                </div>
                                <p class="mt-4 text-2xl font-bold text-[#0f3f78]">Rp{{ number_format((float) ($classPrices[$classKey] ?? 0), 0, ',', '.') }}</p>
                            </button>
                        @endforeach
                    </div>

                    <div class="rounded-3xl border border-white/50 bg-white/45 p-4 sm:p-5">
                        @foreach ($seatMap['classes'] as $classKey => $seatClass)
                            <div x-show="selectedClass === '{{ $classKey }}'" x-cloak class="space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $seatClass['short_label'] }}</p>
                                        <h3 class="mt-1 text-xl font-bold text-slate-800 sm:text-2xl">{{ $seatClass['label'] }}</h3>
                                    </div>
                                    <div class="grid gap-1 text-sm text-slate-600 sm:text-right">
                                        <span>{{ $seatClass['available_count'] }} seat tersedia</span>
                                        <span>Harga per penumpang <strong class="text-[#0f3f78]">Rp{{ number_format((float) ($classPrices[$classKey] ?? 0), 0, ',', '.') }}</strong></span>
                                    </div>
                                </div>

                                @include('partials.seat-map', ['seatClass' => $seatClass, 'interactive' => true, 'classKey' => $classKey])
                            </div>
                        @endforeach
                    </div>

                    <div class="text-sm text-slate-600">
                        Terpilih: <span x-text="selectedSeats.length"></span> seat dari <span x-text="selectedPassengers.length"></span> passenger
                    </div>
                    <div class="flex flex-wrap gap-2" x-show="selectedSeats.length > 0" x-cloak>
                        <template x-for="seatLabel in selectedSeatLabels()" :key="seatLabel">
                            <span class="rounded-full border border-[#0f3f78]/20 bg-[#0f3f78] px-3 py-1 text-xs font-semibold text-white" x-text="seatLabel"></span>
                        </template>
                    </div>
                </div>

                <div x-show="step === 3" x-cloak>
                    <h2 class="font-heading text-xl font-bold text-[#0f3f78] sm:text-2xl">Booking Summary</h2>
                    <div class="mt-4 space-y-3">
                        <template x-for="(item, index) in summaryItems()" :key="index">
                            <div class="portal-card-soft flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-slate-800" x-text="item.passenger_name"></p>
                                    <p class="text-sm text-slate-500">
                                        Seat <span x-text="item.seat_number"></span> · <span x-text="item.class_label"></span>
                                    </p>
                                </div>
                                <p class="font-semibold text-[#0f3f78]" x-text="item.formatted_price"></p>
                            </div>
                        </template>
                    </div>
                    <div class="mt-4 rounded-xl border border-slate-200 bg-white/70 p-4">
                        <p class="text-sm text-slate-500">Total Price</p>
                        <p class="text-3xl font-bold text-[#0f3f78]" x-text="formattedTotal()"></p>
                    </div>
                </div>

                @error('passengers')
                    <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('seat_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('seat_class')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <button type="button" class="portal-btn-blue" @click="prevStep()" x-show="step > 1">Back</button>
                    <div class="flex items-center gap-3">
                        <button type="button" class="portal-btn-blue" @click="nextStep()" x-show="step < 3">Next</button>
                        <button type="submit" class="portal-btn-gold" x-show="step === 3">Confirm Booking</button>
                    </div>
                </div>
            </form>
        </article>
    </section>

    <script>
        function bookingWizard(passengers, seats, availableSeatIds, classPrices, initialClass) {
            return {
                step: 1,
                passengers,
                seats,
                availableSeatIds,
                classPrices,
                selectedClass: initialClass,
                selectedPassengers: [],
                selectedSeats: [],

                setClass(classKey) {
                    if (this.selectedClass === classKey) return;

                    this.selectedClass = classKey;
                    this.selectedSeats = [];
                },

                isSeatAvailable(seatId) {
                    const seat = this.seats.find(item => item.id === Number(seatId));

                    return this.availableSeatIds.includes(Number(seatId)) && seat?.class === this.selectedClass;
                },

                seatClass(seatId, available) {
                    if (!available) {
                        return 'border-red-300 bg-red-100 text-red-700 cursor-not-allowed';
                    }

                    if (this.selectedSeats.includes(seatId)) {
                        return 'border-[#0f3f78] bg-[#0f3f78] text-white ring-2 ring-[#0f3f78]/35 shadow-[0_8px_18px_rgba(15,63,120,.35)] scale-[1.03]';
                    }

                    return 'border-emerald-300 bg-emerald-100 text-emerald-700 hover:bg-emerald-200';
                },

                toggleSeat(seatId) {
                    if (!this.isSeatAvailable(seatId)) return;

                    const index = this.selectedSeats.indexOf(seatId);

                    if (index >= 0) {
                        this.selectedSeats.splice(index, 1);
                        return;
                    }

                    if (this.selectedSeats.length >= this.selectedPassengers.length) {
                        alert('Jumlah seat harus sama dengan jumlah passenger.');
                        return;
                    }

                    this.selectedSeats.push(seatId);
                },

                classLabel(classKey) {
                    return {
                        first: 'First Class',
                        business: 'Business Class',
                        economy: 'Economy Class',
                    }[classKey] || classKey;
                },

                seatPrice(seatId) {
                    const seat = this.seats.find(item => item.id === Number(seatId));

                    return Number(this.classPrices[seat?.class || this.selectedClass] || 0);
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0,
                    }).format(amount);
                },

                nextStep() {
                    if (this.step === 1 && this.selectedPassengers.length === 0) {
                        alert('Pilih minimal satu passenger.');
                        return;
                    }

                    if (this.step === 2 && this.selectedSeats.length !== this.selectedPassengers.length) {
                        alert('Jumlah seat harus sama dengan jumlah passenger.');
                        return;
                    }

                    this.step = Math.min(this.step + 1, 3);
                },

                prevStep() {
                    this.step = Math.max(this.step - 1, 1);
                },

                summaryItems() {
                    return this.selectedPassengers.map((passengerId, index) => {
                        const passenger = this.passengers.find(item => item.id === Number(passengerId));
                        const seat = this.seats.find(item => item.id === Number(this.selectedSeats[index]));
                        const price = this.seatPrice(seat?.id);

                        return {
                            passenger_id: Number(passengerId),
                            passenger_name: passenger?.name || '-',
                            seat_id: seat?.id || null,
                            seat_number: seat?.seat_number || '-',
                            class_label: this.classLabel(seat?.class || this.selectedClass),
                            formatted_price: this.formatCurrency(price),
                            price,
                        };
                    });
                },

                selectedSeatLabels() {
                    return this.selectedSeats
                        .map((seatId) => this.seats.find(item => item.id === Number(seatId))?.seat_number)
                        .filter(Boolean);
                },

                formattedTotal() {
                    const total = this.summaryItems().reduce((sum, item) => sum + item.price, 0);

                    return this.formatCurrency(total);
                },

                submitForm(event) {
                    if (this.selectedSeats.length !== this.selectedPassengers.length) {
                        alert('Jumlah seat harus sama dengan jumlah passenger.');
                        return;
                    }

                    const container = this.$refs.hiddenInputs;
                    container.innerHTML = '';

                    this.summaryItems().forEach((item, index) => {
                        const passengerInput = document.createElement('input');
                        passengerInput.type = 'hidden';
                        passengerInput.name = `passengers[${index}][passenger_id]`;
                        passengerInput.value = item.passenger_id;
                        container.appendChild(passengerInput);

                        const seatInput = document.createElement('input');
                        seatInput.type = 'hidden';
                        seatInput.name = `passengers[${index}][seat_id]`;
                        seatInput.value = item.seat_id;
                        container.appendChild(seatInput);
                    });

                    event.target.submit();
                }
            };
        }
    </script>
@endsection

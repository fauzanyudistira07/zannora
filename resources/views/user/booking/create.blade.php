@extends('layouts.portal')

@section('title', 'Zannora | Booking')
@section('active', 'flights')

@section('content')
    @php
        $seatMap = $flight->airplane->seats->values();
        $availableSeatIds = $availableSeats->pluck('id')->all();

        $seatBlueprint = $seatMap->map(function ($seat, $index) {
            $seatNumber = strtoupper((string) $seat->seat_number);
            $row = null;
            $column = null;

            if (preg_match('/^([A-Z]+)(\d+)$/', $seatNumber, $matches) === 1) {
                $column = $matches[1];
                $row = (int) $matches[2];
            } elseif (preg_match('/^(\d+)([A-Z]+)$/', $seatNumber, $matches) === 1) {
                $row = (int) $matches[1];
                $column = $matches[2];
            } else {
                $row = $index + 1;
                $column = 'A';
            }

            return collect([
                'id' => $seat->id,
                'seat_number' => $seatNumber,
                'row' => $row,
                'column' => $column,
            ]);
        })->sortBy(fn ($seat) => sprintf('%05d-%s', $seat['row'], $seat['column']))->values();

        $seatColumns = $seatBlueprint->pluck('column')->unique()->sort()->values();

        if ($seatColumns->count() === 1 && $seatBlueprint->count() >= 6) {
            $syntheticColumns = collect(['A', 'B', 'C', 'D', 'E', 'F']);
            $seatBlueprint = $seatBlueprint->values()->map(function ($seat, $index) use ($syntheticColumns) {
                $layoutRow = intdiv($index, 6) + 1;
                $layoutColumn = $syntheticColumns[$index % 6];

                return $seat
                    ->put('layout_row', $layoutRow)
                    ->put('layout_column', $layoutColumn)
                    ->put('display_label', $layoutRow.$layoutColumn);
            });
            $seatColumns = $syntheticColumns;
        } else {
            $seatBlueprint = $seatBlueprint->map(fn ($seat) => $seat
                ->put('layout_row', $seat['row'])
                ->put('layout_column', $seat['column'])
                ->put('display_label', $seat['row'].$seat['column']));
        }

        $splitPoint = (int) ceil($seatColumns->count() / 2);
        $leftColumns = $seatColumns->slice(0, $splitPoint)->values();
        $rightColumns = $seatColumns->slice($splitPoint)->values();
        $leftColumnsCount = max($leftColumns->count(), 1);
        $rightColumnsCount = max($rightColumns->count(), 1);
        $seatRows = $seatBlueprint
            ->groupBy('layout_row')
            ->sortKeys()
            ->map(fn ($seats) => $seats->keyBy('layout_column'));
    @endphp

    <section x-data="bookingWizard(
        {{ Js::from($passengers->map(fn ($p) => ['id' => $p->id, 'name' => $p->full_name])->values()) }},
        {{ Js::from($seatMap->map(fn ($s) => ['id' => $s->id, 'seat_number' => $s->seat_number, 'class' => $s->class])->values()) }},
        {{ Js::from($availableSeatIds) }},
        {{ (float) $flight->price }}
    )" class="space-y-6">
        <article class="portal-card">
            <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $flight->airline->name }}</p>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h1 class="mt-1 font-heading text-4xl font-bold text-slate-800">Booking {{ $flight->flight_number }}</h1>
            </div>
            <p class="mt-2 text-slate-600">
                {{ $flight->departureAirport->city }} ({{ $flight->departureAirport->code }})
                → {{ $flight->arrivalAirport->city }} ({{ $flight->arrivalAirport->code }})
            </p>
            <p class="mt-1 text-sm text-slate-600">
                {{ $flight->departure_time->format('d M Y H:i') }} - {{ $flight->arrival_time->format('d M Y H:i') }}
            </p>
        </article>

        <article class="portal-card">
            <div class="flex flex-wrap items-center gap-3">
                <span :class="step === 1 ? 'portal-status-confirmed' : 'portal-status-default'">Step 1 - Passenger</span>
                <span :class="step === 2 ? 'portal-status-confirmed' : 'portal-status-default'">Step 2 - Seat</span>
                <span :class="step === 3 ? 'portal-status-confirmed' : 'portal-status-default'">Step 3 - Summary</span>
            </div>

            <form method="POST" action="{{ route('booking.store') }}" class="mt-6" @submit.prevent="submitForm($event)">
                @csrf
                <input type="hidden" name="flight_id" value="{{ $flight->id }}">
                <div x-ref="hiddenInputs"></div>

                <div x-show="step === 1" x-cloak>
                    <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Pilih Passenger</h2>
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

                <div x-show="step === 2" x-cloak>
                    <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Pilih Seat</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Pilih seat sesuai jumlah passenger terpilih. Layout seat mengikuti peta kabin.
                    </p>
                    <div class="mt-4 rounded-[28px] border border-slate-200 bg-slate-50/90 p-4 sm:p-5">
                        <div class="mb-4 flex items-center gap-4 text-xs font-semibold">
                            <span class="inline-flex items-center gap-1 text-emerald-700"><span class="h-3 w-3 rounded-full bg-emerald-300"></span>Tersedia</span>
                            <span class="inline-flex items-center gap-1 text-emerald-800"><span class="h-3 w-3 rounded-full bg-emerald-500"></span>Dipilih</span>
                            <span class="inline-flex items-center gap-1 text-red-700"><span class="h-3 w-3 rounded-full bg-red-300"></span>Terisi</span>
                        </div>

                        <div class="overflow-x-auto">
                            <div class="min-w-[640px] space-y-3">
                                <div class="mx-auto h-5 w-36 rounded-b-full bg-slate-300/70"></div>

                                <div class="grid grid-cols-[46px_1fr_72px_1fr_46px] items-center gap-3 text-center text-xs font-semibold tracking-wide text-slate-500">
                                    <span>Row</span>
                                    <div class="grid gap-1.5" style="grid-template-columns: repeat({{ $leftColumnsCount }}, minmax(0, 1fr));">
                                        @foreach ($leftColumns as $column)
                                            <span>{{ $column }}</span>
                                        @endforeach
                                    </div>
                                    <span class="uppercase text-slate-400">Aisle</span>
                                    <div class="grid gap-1.5" style="grid-template-columns: repeat({{ $rightColumnsCount }}, minmax(0, 1fr));">
                                        @foreach ($rightColumns as $column)
                                            <span>{{ $column }}</span>
                                        @endforeach
                                    </div>
                                    <span>Row</span>
                                </div>

                                @foreach ($seatRows as $rowNumber => $rowSeats)
                                    <div class="grid grid-cols-[46px_1fr_72px_1fr_46px] items-center gap-3">
                                        <span class="text-center text-xs font-semibold text-slate-500">{{ $rowNumber }}</span>
                                        <div class="grid gap-1.5" style="grid-template-columns: repeat({{ $leftColumnsCount }}, minmax(0, 1fr));">
                                            @foreach ($leftColumns as $column)
                                                @php($seat = $rowSeats->get($column))
                                                @if ($seat)
                                                    @php($isAvailable = in_array($seat['id'], $availableSeatIds, true))
                                                    <button
                                                        type="button"
                                                        @click="toggleSeat({{ $seat['id'] }})"
                                                        :disabled="!isSeatAvailable({{ $seat['id'] }})"
                                                        class="relative h-9 rounded-md border text-[11px] font-semibold leading-none transition duration-200"
                                                        :class="seatClass({{ $seat['id'] }}, {{ $isAvailable ? 'true' : 'false' }})"
                                                        title="{{ $seat['seat_number'] }}"
                                                    >
                                                        {{ $seat['display_label'] }}
                                                        <span
                                                            x-show="selectedSeats.includes({{ $seat['id'] }})"
                                                            x-cloak
                                                            class="absolute -right-1 -top-1 grid h-4 w-4 place-content-center rounded-full bg-white text-[10px] font-bold text-[#0f3f78] shadow"
                                                        >
                                                            ✓
                                                        </span>
                                                    </button>
                                                @else
                                                    <span class="h-9"></span>
                                                @endif
                                            @endforeach
                                        </div>

                                        <span class="text-center text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">Aisle</span>

                                        <div class="grid gap-1.5" style="grid-template-columns: repeat({{ $rightColumnsCount }}, minmax(0, 1fr));">
                                            @foreach ($rightColumns as $column)
                                                @php($seat = $rowSeats->get($column))
                                                @if ($seat)
                                                    @php($isAvailable = in_array($seat['id'], $availableSeatIds, true))
                                                    <button
                                                        type="button"
                                                        @click="toggleSeat({{ $seat['id'] }})"
                                                        :disabled="!isSeatAvailable({{ $seat['id'] }})"
                                                        class="relative h-9 rounded-md border text-[11px] font-semibold leading-none transition duration-200"
                                                        :class="seatClass({{ $seat['id'] }}, {{ $isAvailable ? 'true' : 'false' }})"
                                                        title="{{ $seat['seat_number'] }}"
                                                    >
                                                        {{ $seat['display_label'] }}
                                                        <span
                                                            x-show="selectedSeats.includes({{ $seat['id'] }})"
                                                            x-cloak
                                                            class="absolute -right-1 -top-1 grid h-4 w-4 place-content-center rounded-full bg-white text-[10px] font-bold text-[#0f3f78] shadow"
                                                        >
                                                            ✓
                                                        </span>
                                                    </button>
                                                @else
                                                    <span class="h-9"></span>
                                                @endif
                                            @endforeach
                                        </div>
                                        <span class="text-center text-xs font-semibold text-slate-500">{{ $rowNumber }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-sm text-slate-600">
                        Terpilih: <span x-text="selectedSeats.length"></span> seat dari <span x-text="selectedPassengers.length"></span> passenger
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2" x-show="selectedSeats.length > 0" x-cloak>
                        <template x-for="seatLabel in selectedSeatLabels()" :key="seatLabel">
                            <span class="rounded-full border border-[#0f3f78]/20 bg-[#0f3f78] px-3 py-1 text-xs font-semibold text-white" x-text="seatLabel"></span>
                        </template>
                    </div>
                </div>

                <div x-show="step === 3" x-cloak>
                    <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Booking Summary</h2>
                    <div class="mt-4 space-y-3">
                        <template x-for="(item, index) in summaryItems()" :key="index">
                            <div class="portal-card-soft flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-slate-800" x-text="item.passenger_name"></p>
                                    <p class="text-sm text-slate-500">Seat <span x-text="item.seat_number"></span></p>
                                </div>
                                <p class="font-semibold text-[#0f3f78]">Rp{{ number_format((float) $flight->price, 0, ',', '.') }}</p>
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
        function bookingWizard(passengers, seats, availableSeatIds, flightPrice) {
            return {
                step: 1,
                passengers,
                seats,
                availableSeatIds,
                flightPrice,
                selectedPassengers: [],
                selectedSeats: [],

                isSeatAvailable(seatId) {
                    return this.availableSeatIds.includes(seatId);
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

                        return {
                            passenger_id: Number(passengerId),
                            passenger_name: passenger?.name || '-',
                            seat_id: seat?.id || null,
                            seat_number: seat?.seat_number || '-',
                        };
                    });
                },

                selectedSeatLabels() {
                    return this.selectedSeats
                        .map((seatId) => this.seats.find(item => item.id === Number(seatId))?.seat_number)
                        .filter(Boolean);
                },

                formattedTotal() {
                    const total = this.selectedPassengers.length * this.flightPrice;
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total);
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

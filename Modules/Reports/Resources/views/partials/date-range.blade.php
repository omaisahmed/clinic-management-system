<form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-end gap-3">
    <div class="w-44">
        <x-label for="date_from">From</x-label>
        <x-date-input name="date_from" id="date_from" :value="request('date_from', $from ?? null)" />
    </div>
    <div class="w-44">
        <x-label for="date_to">To</x-label>
        <x-date-input name="date_to" id="date_to" :value="request('date_to', $to ?? null)" />
    </div>
    <x-button type="submit" variant="secondary" icon="filter">Filter</x-button>
</form>

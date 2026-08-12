<x-app-layout>
    <x-page-header :title="$medicine->name" :subtitle="$medicine->generic_name ?: 'Medicine'">
        <x-slot name="actions">
            @can('medicines.update')
                <x-button :href="route('medicines.edit', $medicine)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            <x-button href="{{ route('medicines.index') }}" variant="ghost" icon="arrow-left">Back to Medicines</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Medicine Details</h3>
        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
            <x-detail label="Name">{{ $medicine->name }}</x-detail>
            <x-detail label="Generic Name">{{ $medicine->generic_name ?: '—' }}</x-detail>
            <x-detail label="Category">{{ $medicine->category ?: '—' }}</x-detail>
            <x-detail label="Brand">{{ $medicine->brand ?: '—' }}</x-detail>
            <x-detail label="Strength">{{ $medicine->strength ?: '—' }}</x-detail>
            <x-detail label="Unit">{{ $medicine->unit ?: '—' }}</x-detail>
            <x-detail label="Stock">
                <span class="flex items-center gap-2">
                    {{ $medicine->stock }}
                    @if ($medicine->is_low_stock)
                        <x-badge variant="red">Low stock</x-badge>
                    @else
                        <x-badge variant="green">In stock</x-badge>
                    @endif
                </span>
            </x-detail>
            <x-detail label="Reorder Level">{{ $medicine->reorder_level }}</x-detail>
            <x-detail label="Cost Price">{{ $medicine->cost_price !== null ? money($medicine->cost_price) : '—' }}</x-detail>
            <x-detail label="Selling Price">{{ $medicine->selling_price !== null ? money($medicine->selling_price) : '—' }}</x-detail>
            <x-detail label="Expiry Date">
                @if ($medicine->expiry_date)
                    <span class="{{ $medicine->expiry_date->lte(now()->addDays(30)) ? 'text-red-600 dark:text-red-400' : '' }}">
                        {{ $medicine->expiry_date->format('M d, Y') }}
                    </span>
                @else
                    —
                @endif
            </x-detail>
            <x-detail label="Description">{{ $medicine->description ?: '—' }}</x-detail>
        </dl>
    </div>
</x-app-layout>

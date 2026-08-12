<x-app-layout>
    <x-page-header :title="$document->title" :subtitle="$document->patient?->full_name ?: 'No patient assigned'">
        <x-slot name="actions">
            <x-button :href="route('documents.download', $document)" variant="secondary" size="sm" icon="arrow-right">Download</x-button>
            @can('documents.update')
                <x-button :href="route('documents.edit', $document)" variant="secondary" icon="pencil">Edit</x-button>
            @endcan
            <x-button href="{{ route('documents.index') }}" variant="ghost" icon="arrow-left">Back</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card p-6">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Document Details</h3>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <x-detail label="Title">{{ $document->title }}</x-detail>
                    <x-detail label="Patient">
                        @if ($document->patient)
                            <a href="{{ route('patients.show', $document->patient) }}" class="text-[var(--color-primary)] hover:underline">
                                {{ $document->patient->full_name }}
                            </a>
                        @else
                            —
                        @endif
                    </x-detail>
                    <x-detail label="Category">{{ $document->category ?: '—' }}</x-detail>
                    <x-detail label="Original File">{{ $document->original_name }}</x-detail>
                    <x-detail label="Size">{{ $document->file_size_label }}</x-detail>
                    <x-detail label="Uploaded By">{{ $document->uploader?->name ?: '—' }}</x-detail>
                    <x-detail label="Uploaded On">{{ $document->created_at->format('M d, Y') }}</x-detail>
                </dl>
            </div>

            @if ($document->notes)
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Notes</h3>
                    <p class="mt-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-300">{{ $document->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

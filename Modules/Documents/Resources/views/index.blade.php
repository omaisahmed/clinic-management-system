<x-app-layout>
    <x-page-header title="Documents" subtitle="Patient document vault">
        <x-slot name="actions">
            @can('documents.create')
                <x-button href="{{ route('documents.create') }}" icon="folder">Upload Document</x-button>
            @endcan
        </x-slot>
    </x-page-header>

    <x-alerts />

    <!-- Filters -->
    <div class="card mb-4 p-4">
        <form method="GET" action="{{ route('documents.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-64 flex-1">
                <x-label for="q">Search</x-label>
                <x-input name="q" id="q" :value="request('q')" placeholder="Title or category..." />
            </div>
            <div class="w-44">
                <x-label for="category">Category</x-label>
                <x-select name="category" id="category" :options="$categories" :value="request('category')" placeholder="Any" />
            </div>
            <x-button type="submit" variant="secondary" icon="search">Search</x-button>
            @if (request()->hasAny(['q', 'category']))
                <x-button href="{{ route('documents.index') }}" variant="ghost" icon="x-mark">Clear</x-button>
            @endif
        </form>
    </div>

    <div class="card overflow-hidden">
        <x-table :headers="['Title', 'Patient', 'Category', 'Size', 'Uploaded', '']">
            @forelse ($documents as $document)
                <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/40">
                    <td class="td">
                        <a href="{{ route('documents.show', $document) }}" class="font-medium text-slate-900 hover:underline dark:text-white">
                            {{ $document->title }}
                        </a>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $document->original_name }}</p>
                    </td>
                    <td class="td">
                        @if ($document->patient)
                            <a href="{{ route('patients.show', $document->patient) }}" class="text-slate-700 hover:underline dark:text-slate-200">
                                {{ $document->patient->full_name }}
                            </a>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td">
                        @if ($document->category)
                            <x-badge>{{ $document->category }}</x-badge>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="td text-slate-500">{{ $document->file_size_label }}</td>
                    <td class="td text-slate-500">{{ $document->created_at->format('M d, Y') }}</td>
                    <td class="td">
                        <div class="flex items-center justify-end gap-1">
                            <x-button :href="route('documents.show', $document)" variant="ghost" size="sm" icon="eye">View</x-button>
                            @can('documents.download')
                                <x-button :href="route('documents.download', $document)" variant="ghost" size="sm" icon="download">Download</x-button>
                            @endcan
                            @can('documents.update')
                                <x-button :href="route('documents.edit', $document)" variant="ghost" size="sm" icon="pencil">Edit</x-button>
                            @endcan
                            @can('documents.delete')
                                <form method="POST" action="{{ route('documents.destroy', $document) }}"
                                      x-data x-on:submit.prevent="$dispatch('confirm-submit')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="trash" class="text-red-600">Delete</x-button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-empty-state message="No documents found." icon="folder"
                                       :actionLabel="auth()->user()->can('documents.create') ? 'Upload Document' : null"
                                       :actionHref="auth()->user()->can('documents.create') ? route('documents.create') : null" />
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <x-pagination :paginator="$documents" />
        </div>
    </div>
</x-app-layout>

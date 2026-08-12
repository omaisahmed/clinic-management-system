<x-app-layout>
    <x-page-header title="Edit Document" :subtitle="$document->title">
        <x-slot name="actions">
            <x-button :href="route('documents.show', $document)" variant="secondary" icon="arrow-left">Back to Document</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')
        @include('documents::partials.form')
    </form>
</x-app-layout>

<x-app-layout>
    <x-page-header title="Upload Document" subtitle="Attach a file to a patient's record">
        <x-slot name="actions">
            <x-button href="{{ route('documents.index') }}" variant="secondary" icon="arrow-left">Back to Documents</x-button>
        </x-slot>
    </x-page-header>

    <x-alerts />

    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @include('documents::partials.form', ['document' => null])
    </form>
</x-app-layout>

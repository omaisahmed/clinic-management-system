@if (session('status'))
    <div class="mb-4">
        <x-alert type="success">{{ session('status') }}</x-alert>
    </div>
@endif

@if ($errors->any())
    <div class="mb-4">
        <x-alert type="error" :title="'Something went wrong.'" :dismissible="true">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    </div>
@endif

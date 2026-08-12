@props(['name' => null, 'value' => null, 'required' => false])

<label {{ $attributes->merge(['class' => 'label']) }}>
    {{ $slot }}
    @if ($required)
        <span class="text-red-500">*</span>
    @endif
</label>

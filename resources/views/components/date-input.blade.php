@props(['name' => null, 'value' => null, 'min' => null, 'max' => null])

<x-input type="date"
         :name="$name"
         :value="$value"
         :min="$min"
         :max="$max"
         {{ $attributes }} />

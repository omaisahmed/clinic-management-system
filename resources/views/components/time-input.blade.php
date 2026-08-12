@props(['name' => null, 'value' => null, 'step' => 300])

<x-input type="time"
         :name="$name"
         :value="$value"
         :step="$step"
         {{ $attributes }} />

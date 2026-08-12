<x-app-layout>
    <x-page-header title="Settings" subtitle="Manage clinic configuration and integrations" />

    <x-alerts />

    <div class="flex flex-col gap-6 lg:flex-row">
        <!-- Tabs -->
        <div class="lg:w-56 lg:shrink-0">
            <nav class="flex gap-1 overflow-x-auto rounded-xl border border-slate-200 bg-white p-1.5 lg:flex-col dark:border-slate-700 dark:bg-slate-800">
                @foreach ($groups as $key => $group)
                    <a href="{{ route('settings.index', ['group' => $key]) }}"
                       class="flex shrink-0 items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition {{ $active === $key ? 'bg-[var(--color-primary)] text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700' }}">
                        <x-icon :name="$group['icon']" class="h-4 w-4" />
                        {{ $group['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <!-- Active group -->
        <div class="min-w-0 flex-1">
            <form method="POST" action="{{ route('settings.update', $active) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="card p-6">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $groups[$active]['label'] }}</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Configure {{ strtolower($groups[$active]['label']) }} settings.</p>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">
                        @if ($active === 'clinic')
                            <div class="sm:col-span-2">
                                <x-label for="logo">Clinic Logo</x-label>
                                <x-file-upload name="logo" accept="image/*" label="Upload clinic logo" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-label for="favicon">Favicon</x-label>
                                <x-file-upload name="favicon" accept="image/*" label="Upload favicon (32x32)" />
                            </div>
                        @endif

                        @foreach ($groups[$active]['fields'] as $field)
                            @php
                                $key = $field['key'];
                                $isEncrypted = (bool) ($field['encrypted'] ?? false);

                                if ($active === 'clinic') {
                                    $column = substr($key, strlen('clinic.'));
                                    $value = $clinic?->{$column};
                                } else {
                                    $value = $values[$key]['value'] ?? null;
                                }
                            @endphp

                            <div class="{{ $field['type'] === \Modules\Settings\Enums\SettingType::Textarea ? 'sm:col-span-2' : '' }}">
                                <x-label for="{{ $key }}">
                                    {{ $field['label'] }}
                                    @if ($isEncrypted)
                                        <span class="ml-1 text-xs text-slate-400">(encrypted)</span>
                                    @endif
                                </x-label>

                                @if ($field['type'] === \Modules\Settings\Enums\SettingType::Boolean)
                                    <x-checkbox name="{{ $key }}" label="{{ $field['label'] }}" :checked="(bool) $value" />
                                @elseif ($field['type'] === \Modules\Settings\Enums\SettingType::Textarea)
                                    <x-textarea name="{{ $key }}" :rows="3">{{ $value }}</x-textarea>
                                @elseif ($field['type'] === \Modules\Settings\Enums\SettingType::Color)
                                    <div class="flex items-center gap-3">
                                        <input type="color" name="{{ $key }}" value="{{ $value ?? '#0d9488' }}" class="h-10 w-16 cursor-pointer rounded border border-slate-300 dark:border-slate-600" />
                                        <x-input name="{{ $key }}" :value="$value" class="!w-32" />
                                    </div>
                                @elseif (! empty($field['searchable']))
                                    <x-search-select name="{{ $key }}" :options="$field['options'] ?? []" :value="$value" placeholder="Select..." />
                                @elseif (! empty($field['options']))
                                    <x-select name="{{ $key }}" :options="$field['options']" :value="$value" placeholder="Select..." />
                                @else
                                    <x-input name="{{ $key }}"
                                             :type="$isEncrypted ? 'password' : 'text'"
                                             :value="$isEncrypted ? '' : $value"
                                             :placeholder="$isEncrypted ? '•••••••••••••••• (unchanged)' : ''" />
                                    @if ($isEncrypted)
                                        <p class="mt-1 text-xs text-slate-400">Leave blank to keep the current value.</p>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-400">Changes are saved immediately for your clinic.</p>
                    <x-button type="submit" icon="check">Save {{ $groups[$active]['label'] }} Settings</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

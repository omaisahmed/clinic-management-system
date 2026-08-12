@props(['nav'])

<div class="flex h-full flex-col">
    <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg text-white shadow-md"
             style="background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));">
            <x-icon name="stethoscope" class="h-5 w-5" />
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm font-bold text-white">{{ current_clinic()?->name ?? config('app.name') }}</p>
            <p class="truncate text-xs text-slate-400">{{ current_clinic()?->tagline ?? 'Clinic Management System' }}</p>
        </div>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
        @foreach ($nav as $section)
            @php
                $visible = collect($section['items'])->filter(fn ($item) => auth()->user()->can($item['permission']) && Route::has($item['route']))->values();
            @endphp
            @if ($visible->isEmpty())
                @continue
            @endif

            <div>
                <p class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $section['group'] }}</p>
                <ul class="space-y-0.5">
                    @foreach ($visible as $item)
                        @php
                            $active = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
                        @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition {{ $active ? 'bg-[var(--color-primary)] text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                                <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                                <span>{{ $item['label'] }}</span>
                                @if ($active)
                                    <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="border-t border-slate-800 p-3">
        <a href="{{ route('profile.edit') }}"
           class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition hover:bg-slate-800/60">
            <x-avatar :user="auth()->user()" :size="32" />
            <div class="min-w-0 flex-1">
                <p class="truncate font-medium text-slate-200">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->roles->first()?->name }}</p>
            </div>
            <x-icon name="cog" class="h-4 w-4 shrink-0 text-slate-500 group-hover:text-slate-300" />
        </a>
    </div>
</div>

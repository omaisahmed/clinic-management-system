<div x-data="toasts"
     data-flash="{{ json_encode(session('toast', [])) }}"
     class="pointer-events-none fixed bottom-4 right-4 z-[70] flex w-80 flex-col gap-2">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-4 opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-4 opacity-0"
             class="pointer-events-auto flex items-start gap-3 rounded-xl border bg-white p-4 shadow-lg dark:bg-slate-800"
             :class="{
                 'border-green-200 dark:border-green-800': toast.type === 'success',
                 'border-red-200 dark:border-red-800': toast.type === 'error',
                 'border-amber-200 dark:border-amber-800': toast.type === 'warning',
                 'border-blue-200 dark:border-blue-800': toast.type === 'info',
             }">
            <x-icon name="check-circle" x-show="toast.type === 'success'" class="mt-0.5 h-5 w-5 shrink-0 text-green-500" />
            <x-icon name="x-circle" x-show="toast.type === 'error'" class="mt-0.5 h-5 w-5 shrink-0 text-red-500" />
            <x-icon name="alert-triangle" x-show="toast.type === 'warning'" class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
            <x-icon name="information-circle" x-show="toast.type === 'info'" class="mt-0.5 h-5 w-5 shrink-0 text-blue-500" />
            <p class="flex-1 text-sm text-slate-700 dark:text-slate-200" x-text="toast.message"></p>
            <button type="button" x-on:click="remove(toast.id)" class="shrink-0 text-slate-400 hover:text-slate-600">
                <x-icon name="x-mark" class="h-4 w-4" />
            </button>
        </div>
    </template>
</div>

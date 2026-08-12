<div x-data="confirmForm">
    <div x-cloak
         x-show="activeForm"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-sm"></div>

    <div x-cloak
         x-show="activeForm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="translate-y-4 opacity-0 sm:scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
         x-transition:leave-end="translate-y-4 opacity-0 sm:scale-95"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="card w-full max-w-sm overflow-hidden">
            <div class="p-5 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/40">
                    <x-icon name="alert-triangle" class="h-5 w-5 text-red-600 dark:text-red-400" />
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white" x-text="activeForm ? 'Are you sure?' : ''"></h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">This action cannot be undone.</p>
            </div>
            <div class="flex gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                <x-button type="button" variant="secondary" class="flex-1" x-on:click="activeForm = null">Cancel</x-button>
                <x-button type="button" variant="danger" class="flex-1" x-on:click="proceed()">Delete</x-button>
            </div>
        </div>
    </div>
</div>

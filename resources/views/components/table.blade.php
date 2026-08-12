@props(['headers' => [], 'emptyMessage' => null])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        @if (count($headers) > 0)
            <thead class="bg-slate-50 dark:bg-slate-800/60">
                <tr>
                    @foreach ($headers as $header)
                        <th scope="col" class="th">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-800">
            {{ $slot }}

            @if (empty(trim($slot)) && $emptyMessage)
                <tr>
                    <td colspan="99">
                        <x-empty-state :message="$emptyMessage" />
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="mx-auto max-w-4xl space-y-6">
    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Prescription Details</h3>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-label for="patient_id" :required="true">Patient</x-label>
                <x-select name="patient_id" id="patient_id" :options="$patients" :value="old('patient_id', $prescription->patient_id ?? request('patient'))" placeholder="Select patient" />
            </div>
            <div>
                <x-label for="doctor_id">Doctor</x-label>
                <x-select name="doctor_id" id="doctor_id" :options="$doctors" :value="old('doctor_id', $prescription->doctor_id ?? '')" placeholder="Unassigned" />
            </div>
            <div>
                <x-label for="visit_id">Visit</x-label>
                <x-select name="visit_id" id="visit_id" :options="$visits" :value="old('visit_id', $prescription->visit_id ?? '')" placeholder="None" />
            </div>
            @if (isset($prescription) && $prescription !== null)
                <div>
                    <x-label for="status">Status</x-label>
                    <x-select name="status" id="status" :options="$statuses" :value="old('status', $prescription->status?->value ?? '')" placeholder="Select status" />
                </div>
            @endif
            <div class="sm:col-span-2">
                <x-label for="notes">Notes</x-label>
                <x-textarea name="notes" id="notes" rows="3" placeholder="Additional notes">{{ old('notes', $prescription->notes ?? '') }}</x-textarea>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Medications</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Add one or more medications to this prescription.</p>

        <div class="mt-5 space-y-4"
             x-data="{ items: @js(old('items', isset($prescription) ? $prescription->items->map(fn($i) => ['name'=>$i->name,'dosage'=>$i->dosage,'frequency'=>$i->frequency,'duration'=>$i->duration,'instructions'=>$i->instructions])->values()->all() : [['name'=>'','dosage'=>'','frequency'=>'','duration'=>'','instructions'=>'']])) }">
            <template x-for="(item, i) in items" :key="i">
                <div class="grid gap-3 rounded-xl border border-slate-200 p-4 dark:border-slate-700 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <input type="text" x-model="item.name" :name="'items['+i+'][name]'" placeholder="Medicine name" class="input">
                    </div>
                    <div>
                        <input type="text" x-model="item.dosage" :name="'items['+i+'][dosage]'" placeholder="Dosage (e.g. 500 mg)" class="input">
                    </div>
                    <div>
                        <input type="text" x-model="item.frequency" :name="'items['+i+'][frequency]'" placeholder="Frequency (e.g. 3x daily)" class="input">
                    </div>
                    <div>
                        <input type="text" x-model="item.duration" :name="'items['+i+'][duration]'" placeholder="Duration (e.g. 5 days)" class="input">
                    </div>
                    <div>
                        <input type="text" x-model="item.instructions" :name="'items['+i+'][instructions]'" placeholder="Instructions (optional)" class="input">
                    </div>
                    <div class="flex items-center justify-end sm:col-span-2">
                        <x-button type="button" variant="ghost" size="sm" icon="trash" class="text-red-600" x-on:click="items.splice(i, 1)" x-show="items.length > 1">Remove</x-button>
                    </div>
                </div>
            </template>

            <x-button type="button" variant="secondary" icon="plus" x-on:click="items.push({ name: '', dosage: '', frequency: '', duration: '', instructions: '' })">Add Medication</x-button>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pb-4">
        <x-button href="{{ route('prescriptions.index') }}" variant="ghost">Cancel</x-button>
        <x-button type="submit" icon="check">
            {{ isset($prescription) && $prescription !== null ? 'Save Changes' : 'Create Prescription' }}
        </x-button>
    </div>
</div>

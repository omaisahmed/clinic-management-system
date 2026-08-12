<div class="mx-auto max-w-4xl space-y-6">
    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Visit Details</h3>
        <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-label for="patient_id" :required="true">Patient</x-label>
                <x-select name="patient_id" id="patient_id" :options="$patients" :value="old('patient_id', $visit->patient_id ?? request('patient'))" placeholder="Select patient" />
            </div>
            <div>
                <x-label for="doctor_id">Doctor</x-label>
                <x-select name="doctor_id" id="doctor_id" :options="$doctors" :value="old('doctor_id', $visit->doctor_id ?? '')" placeholder="Unassigned" />
            </div>
            <div>
                <x-label for="appointment_id">Appointment</x-label>
                <x-select name="appointment_id" id="appointment_id" :options="$appointments" :value="old('appointment_id', $visit->appointment_id ?? '')" placeholder="None" />
            </div>
            <div>
                <x-label for="visit_date" :required="true">Visit Date</x-label>
                <x-date-input name="visit_date" id="visit_date" :value="old('visit_date', $visit?->visit_date?->format('Y-m-d') ?? '')" />
            </div>
            @if (isset($visit) && $visit !== null)
                <div>
                    <x-label for="status">Status</x-label>
                    <x-select name="status" id="status" :options="$statuses" :value="old('status', $visit->status?->value ?? '')" placeholder="Select status" />
                </div>
            @endif
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Vitals</h3>
        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <x-label for="temperature">Temperature (°C)</x-label>
                <x-input type="number" step="0.1" name="temperature" id="temperature" placeholder="36.5" :value="old('temperature', $visit->temperature ?? '')" />
            </div>
            <div>
                <x-label for="blood_pressure">Blood Pressure</x-label>
                <x-input name="blood_pressure" id="blood_pressure" placeholder="120/80" maxlength="20" :value="old('blood_pressure', $visit->blood_pressure ?? '')" />
            </div>
            <div>
                <x-label for="heart_rate">Heart Rate (bpm)</x-label>
                <x-input type="number" name="heart_rate" id="heart_rate" placeholder="72" :value="old('heart_rate', $visit->heart_rate ?? '')" />
            </div>
            <div>
                <x-label for="respiratory_rate">Respiratory Rate</x-label>
                <x-input type="number" name="respiratory_rate" id="respiratory_rate" placeholder="16" :value="old('respiratory_rate', $visit->respiratory_rate ?? '')" />
            </div>
            <div>
                <x-label for="weight">Weight (kg)</x-label>
                <x-input type="number" step="0.01" name="weight" id="weight" placeholder="70.00" :value="old('weight', $visit->weight ?? '')" />
            </div>
            <div>
                <x-label for="height">Height (cm)</x-label>
                <x-input type="number" step="0.01" name="height" id="height" placeholder="170.00" :value="old('height', $visit->height ?? '')" />
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Clinical Notes</h3>
        <div class="mt-5 grid gap-5">
            <div>
                <x-label for="chief_complaint">Chief Complaint</x-label>
                <x-textarea name="chief_complaint" id="chief_complaint" rows="3" placeholder="Reason for the visit...">{{ old('chief_complaint', $visit->chief_complaint ?? '') }}</x-textarea>
            </div>
            <div>
                <x-label for="diagnosis">Diagnosis</x-label>
                <x-textarea name="diagnosis" id="diagnosis" rows="3" placeholder="Diagnosis / findings...">{{ old('diagnosis', $visit->diagnosis ?? '') }}</x-textarea>
            </div>
            <div>
                <x-label for="notes">Notes</x-label>
                <x-textarea name="notes" id="notes" rows="3" placeholder="Additional notes">{{ old('notes', $visit->notes ?? '') }}</x-textarea>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3 pb-4">
        <x-button href="{{ route('visits.index') }}" variant="ghost">Cancel</x-button>
        <x-button type="submit" icon="check">
            {{ isset($visit) && $visit !== null ? 'Save Changes' : 'Start Visit' }}
        </x-button>
    </div>
</div>

<div class="card max-w-3xl p-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-label for="patient_id" :required="true">Patient</x-label>
            <x-select name="patient_id" id="patient_id" :options="$patients"
                      :value="old('patient_id', $appointment->patient_id ?? $patientId ?? '')"
                      placeholder="Select patient" />
        </div>
        <div class="sm:col-span-2">
            <x-label for="doctor_id">Doctor</x-label>
            <x-select name="doctor_id" id="doctor_id" :options="$doctors->pluck('name', 'id')"
                      :value="old('doctor_id', $appointment->doctor_id ?? '')"
                      placeholder="Unassigned" />
        </div>
        <div>
            <x-label for="appointment_type" :required="true">Appointment Type</x-label>
            <x-select name="appointment_type" id="appointment_type" :options="$types"
                      :value="old('appointment_type', $appointment->appointment_type?->value ?? '')"
                      placeholder="Select type" />
        </div>
        <div>
            <x-label for="status">Status</x-label>
            <x-select name="status" id="status" :options="$statuses"
                      :value="old('status', $appointment->status?->value ?? '')"
                      placeholder="Select status" />
        </div>
        <div>
            <x-label for="appointment_date" :required="true">Date</x-label>
            <x-date-input name="appointment_date" id="appointment_date"
                          :value="old('appointment_date', $appointment?->appointment_date?->format('Y-m-d') ?? '')" />
        </div>
        <div>
            <x-label for="start_time" :required="true">Time</x-label>
            <x-time-input name="start_time" id="start_time"
                          :value="old('start_time', $appointment->start_time ?? '')" />
        </div>
        <div>
            <x-label for="duration">Duration (minutes)</x-label>
            <x-input type="number" name="duration" id="duration" min="5" max="240"
                     :value="old('duration', $appointment->duration ?? 30)" />
        </div>
        <div class="sm:col-span-2">
            <x-label for="reason">Reason</x-label>
            <x-input name="reason" id="reason" placeholder="Reason for the appointment"
                     :value="old('reason', $appointment->reason ?? '')" />
        </div>
        <div class="sm:col-span-2">
            <x-label for="notes">Notes</x-label>
            <x-textarea name="notes" id="notes" rows="3" placeholder="Additional notes">{{ old('notes', $appointment->notes ?? '') }}</x-textarea>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <x-button :href="route('appointments.index')" variant="ghost">Cancel</x-button>
        <x-button type="submit" icon="check">{{ isset($appointment) ? 'Save Appointment' : 'Create Appointment' }}</x-button>
    </div>
</div>

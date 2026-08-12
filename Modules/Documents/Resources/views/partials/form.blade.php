<div class="card max-w-3xl p-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <x-label for="patient_id" :required="true">Patient</x-label>
            <x-select name="patient_id" id="patient_id" :options="$patients"
                      :value="old('patient_id', $document->patient_id ?? $patientId ?? '')"
                      placeholder="Select patient" />
        </div>
        <div>
            <x-label for="title" :required="true">Title</x-label>
            <x-input name="title" id="title" :required="true" placeholder="e.g. Blood test report" :value="old('title', $document->title ?? '')" />
        </div>
        <div>
            <x-label for="category">Category</x-label>
            <x-input name="category" id="category" placeholder="e.g. Reports, Consent" :value="old('category', $document->category ?? '')" />
        </div>
        <div class="sm:col-span-2">
            <x-label for="file">Document File</x-label>
            <x-file-upload name="file" accept=".pdf,image/*,.doc,.docx" label="Choose document" />
            @if (isset($document) && $document->file_path)
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    Current file: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $document->original_name }}</span>
                    ({{ $document->file_size_label }}) · leave empty to keep the current file.
                </p>
            @endif
        </div>
        <div class="sm:col-span-2">
            <x-label for="notes">Notes</x-label>
            <x-textarea name="notes" id="notes" rows="3" placeholder="Additional notes">{{ old('notes', $document->notes ?? '') }}</x-textarea>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <x-button :href="route('documents.index')" variant="ghost">Cancel</x-button>
        <x-button type="submit" icon="check">{{ isset($document) ? 'Save Changes' : 'Upload Document' }}</x-button>
    </div>
</div>

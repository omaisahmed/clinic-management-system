<div class="grid gap-6 lg:grid-cols-3">
    <!-- Photo -->
    <div class="card h-fit p-6 lg:sticky lg:top-20">
        <h3 class="text-base font-semibold text-slate-900 dark:text-white">Photo</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Optional patient photo.</p>
        <div class="mt-4">
            <x-file-upload name="photo" accept="image/*" label="Upload patient photo" :preview="true" />
        </div>
    </div>

    <div class="space-y-6 lg:col-span-2">
        <!-- Basic info -->
        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Basic Information</h3>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="first_name" :required="true">First Name</x-label>
                    <x-input name="first_name" id="first_name" :required="true" placeholder="John" :value="old('first_name', $patient->first_name ?? '')" />
                </div>
                <div>
                    <x-label for="last_name" :required="true">Last Name</x-label>
                    <x-input name="last_name" id="last_name" :required="true" placeholder="Smith" :value="old('last_name', $patient->last_name ?? '')" />
                </div>
                <div>
                    <x-label for="gender">Gender</x-label>
                    <x-select name="gender" id="gender" :options="$genders" :value="old('gender', $patient->gender ?? '')" placeholder="Select gender" />
                </div>
                <div>
                    <x-label for="date_of_birth">Date of Birth</x-label>
                    <x-date-input name="date_of_birth" id="date_of_birth" :value="old('date_of_birth', $patient?->date_of_birth?->format('Y-m-d') ?? '')" max="{{ now()->format('Y-m-d') }}" />
                </div>
                <div>
                    <x-label for="blood_group">Blood Group</x-label>
                    <x-select name="blood_group" id="blood_group" :options="$bloodGroups" :value="old('blood_group', $patient->blood_group ?? '')" placeholder="Select blood group" />
                </div>
                <div>
                    <x-label for="marital_status">Marital Status</x-label>
                    <x-select name="marital_status" id="marital_status" :options="$maritalStatuses" :value="old('marital_status', $patient->marital_status ?? '')" placeholder="Select status" />
                </div>
                <div>
                    <x-label for="occupation">Occupation</x-label>
                    <x-input name="occupation" id="occupation" placeholder="Engineer, teacher..." :value="old('occupation', $patient->occupation ?? '')" />
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Contact Information</h3>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="phone">Phone</x-label>
                    <x-input name="phone" id="phone" placeholder="+1 555 0100" :value="old('phone', $patient->phone ?? '')" />
                </div>
                <div>
                    <x-label for="whatsapp">WhatsApp</x-label>
                    <x-input name="whatsapp" id="whatsapp" placeholder="+1 555 0100" :value="old('whatsapp', $patient->whatsapp ?? '')" />
                </div>
                <div>
                    <x-label for="email">Email</x-label>
                    <x-input type="email" name="email" id="email" placeholder="patient@example.com" :value="old('email', $patient->email ?? '')" />
                </div>
                <div>
                    <x-label for="cnic">CNIC / ID Number</x-label>
                    <x-input name="cnic" id="cnic" placeholder="XXXXX-XXXXXXX-X" :value="old('cnic', $patient->cnic ?? '')" />
                </div>
                <div class="sm:col-span-2">
                    <x-label for="address">Address</x-label>
                    <x-input name="address" id="address" placeholder="Street address" :value="old('address', $patient->address ?? '')" />
                </div>
                <div>
                    <x-label for="city">City</x-label>
                    <x-input name="city" id="city" placeholder="City" :value="old('city', $patient->city ?? '')" />
                </div>
                <div>
                    <x-label for="country">Country</x-label>
                    <x-input name="country" id="country" placeholder="Country" :value="old('country', $patient->country ?? '')" />
                </div>
            </div>
        </div>

        <!-- Emergency -->
        <div class="card p-6">
            <h3 class="text-base font-semibold text-slate-900 dark:text-white">Emergency Contact</h3>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <x-label for="emergency_contact">Contact Name</x-label>
                    <x-input name="emergency_contact" id="emergency_contact" placeholder="Spouse, parent..." :value="old('emergency_contact', $patient->emergency_contact ?? '')" />
                </div>
                <div>
                    <x-label for="emergency_contact_phone">Contact Phone</x-label>
                    <x-input name="emergency_contact_phone" id="emergency_contact_phone" placeholder="+1 555 0100" :value="old('emergency_contact_phone', $patient->emergency_contact_phone ?? '')" />
                </div>
                <div class="sm:col-span-2">
                    <x-label for="notes">Notes</x-label>
                    <x-textarea name="notes" id="notes" rows="3" placeholder="Additional notes">{{ old('notes', $patient->notes ?? '') }}</x-textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pb-4">
            <x-button :href="route('patients.index')" variant="ghost">Cancel</x-button>
            <x-button type="submit" icon="check">
                {{ isset($patient) ? 'Save Changes' : 'Register Patient' }}
            </x-button>
        </div>
    </div>
</div>

<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Settings\Enums\SettingType;
use Modules\Settings\Services\Settings;
use Modules\Settings\Support\SettingsSchema;

class SettingsController extends Controller
{
    public function __construct(private readonly Settings $settings)
    {
    }

    public function index(?string $group = null): View
    {
        Gate::authorize('settings.view');

        $groups = SettingsSchema::groups();
        $active = $group !== null && isset($groups[$group]) ? $group : array_key_first($groups);

        return view('settings::index', [
            'groups' => $groups,
            'active' => $active,
            'values' => $this->settings->all(),
            'clinic' => current_clinic(),
        ]);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        Gate::authorize('settings.update');

        $schema = SettingsSchema::group($group);

        abort_if($schema === null, 404);

        if ($group === 'clinic') {
            $this->updateClinic($request);

            return $this->redirectBack($group, 'Clinic settings updated.');
        }

        foreach ($schema['fields'] as $field) {
            $key = $field['key'];
            $type = $field['type'];
            $encrypted = (bool) ($field['encrypted'] ?? false);

            if ($encrypted && $request->missing($key)) {
                continue;
            }

            if ($type === SettingType::Boolean) {
                $this->settings->set($key, $request->boolean($key), $type, $encrypted);
                continue;
            }

            $raw = $request->input($key);

            // Blank encrypted secrets keep their current value.
            if ($encrypted && ($raw === null || $raw === '')) {
                continue;
            }

            $this->settings->set($key, $raw, $type, $encrypted);
        }

        Audit::record('Settings Changed', 'settings', null, ['group' => $group]);

        return $this->redirectBack($group, 'Settings saved.');
    }

    private function updateClinic(Request $request): void
    {
        $clinic = current_clinic();

        abort_if($clinic === null, 404);

        $data = [];

        foreach (Settings::CLINIC_COLUMNS as $column) {
            if ($request->exists('clinic.' . $column)) {
                $data[$column] = $request->input('clinic.' . $column);
            }
        }

        $clinic->fill($data);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('clinic/logo', 'public');
            $clinic->logo_path = $path;
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('clinic/favicon', 'public');
            $clinic->favicon_path = $path;
        }

        $clinic->save();

        $this->settings->flush($clinic);

        \Modules\Clinics\Services\ClinicContext::reset();
    }

    private function redirectBack(string $group, string $message): RedirectResponse
    {
        return redirect()
            ->route('settings.index', ['group' => $group])
            ->with('toast', [['type' => 'success', 'message' => $message]]);
    }
}

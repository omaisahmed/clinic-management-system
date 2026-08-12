<?php

declare(strict_types=1);

namespace Modules\Medicines\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Medicines\Models\Medicine;

class MedicineController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('medicines.view');

        $medicines = Medicine::query()
            ->search($request->query('q'))
            ->category($request->query('category'))
            ->when($request->boolean('low_stock'), fn ($q) => $q->lowStock())
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('medicines::index', [
            'medicines' => $medicines,
            'categories' => Medicine::query()
                ->whereNotNull('category')
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category', 'category'),
            'totalCount' => Medicine::query()->count(),
            'lowStockCount' => Medicine::query()->lowStock()->count(),
            'expiringCount' => Medicine::query()->expiringSoon(30)->count(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('medicines.create');

        return view('medicines::create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('medicines.create');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'generic_name' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:80'],
            'brand' => ['nullable', 'string', 'max:120'],
            'strength' => ['nullable', 'string', 'max:80'],
            'unit' => ['nullable', 'string', 'max:30'],
            'stock' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $data['clinic_id'] = current_clinic()?->id;

        $medicine = Medicine::create($data);

        Audit::record('Medicine Created', 'medicines', $medicine, [
            'name' => $medicine->name,
        ]);

        return redirect()
            ->route('medicines.show', $medicine)
            ->with('toast', [['type' => 'success', 'message' => "Medicine {$medicine->name} added."]]);
    }

    public function show(Medicine $medicine): View
    {
        Gate::authorize('medicines.view');

        return view('medicines::show', [
            'medicine' => $medicine,
        ]);
    }

    public function edit(Medicine $medicine): View
    {
        Gate::authorize('medicines.update');

        return view('medicines::edit', [
            'medicine' => $medicine,
        ]);
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse
    {
        Gate::authorize('medicines.update');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'generic_name' => ['nullable', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:80'],
            'brand' => ['nullable', 'string', 'max:120'],
            'strength' => ['nullable', 'string', 'max:80'],
            'unit' => ['nullable', 'string', 'max:30'],
            'stock' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $medicine->update($data);

        Audit::record('Medicine Updated', 'medicines', $medicine, [
            'name' => $medicine->name,
        ]);

        return redirect()
            ->route('medicines.show', $medicine)
            ->with('toast', [['type' => 'success', 'message' => 'Medicine updated.']]);
    }

    public function destroy(Medicine $medicine): RedirectResponse
    {
        Gate::authorize('medicines.delete');

        $medicine->delete();

        Audit::record('Medicine Deleted', 'medicines', $medicine, [
            'name' => $medicine->name,
        ]);

        return redirect()
            ->route('medicines.index')
            ->with('toast', [['type' => 'success', 'message' => 'Medicine removed.']]);
    }
}

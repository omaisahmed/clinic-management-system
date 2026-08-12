<?php

declare(strict_types=1);

namespace Modules\Expenses\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Audit\Facades\Audit;
use Modules\Expenses\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('expenses.create');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:expense_categories,name'],
        ]);

        $data['clinic_id'] = current_clinic()?->id;

        $category = ExpenseCategory::create($data);

        Audit::record('Expense Category Created', 'expenses', $category, []);

        return redirect()
            ->route('expenses.index')
            ->with('toast', [['type' => 'success', 'message' => 'Category added.']]);
    }

    public function destroy(ExpenseCategory $category): RedirectResponse
    {
        Gate::authorize('expenses.delete');

        $category->delete();

        Audit::record('Expense Category Deleted', 'expenses', $category, []);

        return redirect()
            ->route('expenses.index')
            ->with('toast', [['type' => 'success', 'message' => 'Category removed.']]);
    }
}

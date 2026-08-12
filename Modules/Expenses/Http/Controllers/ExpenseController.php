<?php

declare(strict_types=1);

namespace Modules\Expenses\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Expenses\Enums\ExpensePaymentMethod;
use Modules\Expenses\Models\Expense;
use Modules\Expenses\Models\ExpenseCategory;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('expenses.view');

        $expenses = Expense::query()
            ->search($request->query('q'))
            ->forCategory($request->query('category'))
            ->betweenDates($request->query('date_from'), $request->query('date_to'))
            ->orderByDesc('expense_date')
            ->paginate(15)
            ->withQueryString();

        $total = Expense::query()
            ->search($request->query('q'))
            ->forCategory($request->query('category'))
            ->betweenDates($request->query('date_from'), $request->query('date_to'))
            ->sum('amount');

        return view('expenses::index', [
            'expenses' => $expenses,
            'categories' => $this->categoryOptions(),
            'total' => $total,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('expenses.create');

        return view('expenses::create', [
            'categories' => $this->categoryOptions(),
            'methods' => ExpensePaymentMethod::choices(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('expenses.create');

        $data = $this->validateExpense($request);

        $data['clinic_id'] = current_clinic()?->id;
        $data['recorded_by'] = auth()->id();

        $expense = Expense::create($data);

        Audit::record('Expense Recorded', 'expenses', $expense, []);

        return redirect()
            ->route('expenses.show', $expense)
            ->with('toast', [['type' => 'success', 'message' => 'Expense recorded.']]);
    }

    public function show(Expense $expense): View
    {
        Gate::authorize('expenses.view');

        $expense->load('category', 'recorder');

        return view('expenses::show', [
            'expense' => $expense,
        ]);
    }

    public function edit(Expense $expense): View
    {
        Gate::authorize('expenses.update');

        return view('expenses::edit', [
            'expense' => $expense,
            'categories' => $this->categoryOptions(),
            'methods' => ExpensePaymentMethod::choices(),
        ]);
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('expenses.update');

        $expense->update($this->validateExpense($request));

        Audit::record('Expense Updated', 'expenses', $expense, []);

        return redirect()
            ->route('expenses.show', $expense)
            ->with('toast', [['type' => 'success', 'message' => 'Expense updated.']]);
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        Gate::authorize('expenses.delete');

        $expense->delete();

        Audit::record('Expense Deleted', 'expenses', $expense, []);

        return redirect()
            ->route('expenses.index')
            ->with('toast', [['type' => 'success', 'message' => 'Expense removed.']]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:expense_categories,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(array_keys(ExpensePaymentMethod::choices()))],
            'notes' => ['nullable', 'string'],
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function categoryOptions(): array
    {
        return ExpenseCategory::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->mapWithKeys(fn (ExpenseCategory $category) => [$category->id => $category->name])
            ->all();
    }
}

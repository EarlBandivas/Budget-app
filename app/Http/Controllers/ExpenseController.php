<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'expense_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense = Expense::create([
            'user_id' => auth()->id(),
            'category' => $validated['category'],
            'expense_type' => $validated['expense_type'],
            'amount' => $validated['amount'],
        ]);

        return redirect()->back()->with('message', 'Expense added successfully');
    }

    public function update(Request $request, Expense $expense)
    {
        // Ensure the user can only update their own expenses
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'category' => 'required|string',
            'expense_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense->update($validated);

        return redirect()->back()->with('message', 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        // Ensure the user can only delete their own expenses
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $expense->delete();

        return redirect()->back()->with('message', 'Expense deleted successfully');
    }

    public function getDashboardData()
    {
        $userId = auth()->id();
        
        $categories = Expense::where('user_id', $userId)
            ->distinct()
            ->pluck('category')
            ->toArray();

        $expenseTypes = Expense::where('user_id', $userId)
            ->distinct()
            ->pluck('expense_type')
            ->toArray();

        return Inertia::render('Dashboard', [
            'savedCategories' => $categories,
            'savedExpenseTypes' => $expenseTypes,
        ]);
    }
}


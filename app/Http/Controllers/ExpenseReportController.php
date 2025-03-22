<?php

namespace App\Http\Controllers;
use App\Models\Expense;
use App\Models\ExpenseReport;
use App\Notifications\ExpenseReportStatusChanged;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseReportController extends Controller
{
 

    public function store(Request $request)
    {
        // Create the report
        $report = ExpenseReport::create([
            'user_id' => auth()->id(),
            'submission_date' => now(),
            'status' => 'pending'
        ]);

        // Get unassigned expenses count before update
        $unassignedCount = Expense::where('user_id', auth()->id())
            ->whereNull('expense_report_id')
            ->count();
        \Log::info("Found {$unassignedCount} unassigned expenses");

        // Associate all unassigned expenses with this report
        $updated = Expense::where('user_id', auth()->id())
            ->whereNull('expense_report_id')
            ->update(['expense_report_id' => $report->id]);
        \Log::info("Updated {$updated} expenses");

        // Verify the association worked by logging or returning the count
        $associatedExpenses = Expense::where('expense_report_id', $report->id)->count();
        \Log::info("Number of expenses associated with report {$report->id}: {$associatedExpenses}");

        return redirect()->back()->with('message', 'Report submitted successfully');
    }

    public function updateStatus(ExpenseReport $report, Request $request)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        $report->update([
            'status' => $request->status
        ]);

        // If rejected, unlink all expenses
        if ($request->status === 'Rejected') {
            $report->expenses()->update(['expense_report_id' => null]);
        }

        // Send notification to the user
        $report->user->notify(new ExpenseReportStatusChanged($request->status, $report->id));

        return redirect()->back()->with('message', 'Report status updated successfully');
    }

    public function adminDashboard()
    {
        $submittedReports = ExpenseReport::with(['user', 'expenses'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                // Add debug logging
                \Log::info("Processing report ID: {$report->id}");
                \Log::info("Expenses count: " . $report->expenses->count());
                
                return [
                    'id' => $report->id,
                    'user_email' => $report->user->email,
                    'user_department' => $report->user->department ?? 'N/A',
                    'submission_date' => $report->submission_date,
                    'status' => $report->status,
                    'expenses' => $report->expenses->map(function ($expense) {
                        \Log::info("Processing expense ID: {$expense->id}");
                        return [
                            'id' => $expense->id,
                            'category' => $expense->category,
                            'expense_type' => $expense->expense_type,
                            'amount' => $expense->amount,
                            'created_at' => $expense->created_at
                        ];
                    })->all() // Make sure we're converting the collection to an array
                ];
            });

       
        
        

        return Inertia::render('Admin/Dashboard', [
            'submittedReports' => $submittedReports
        ]);
    }
}








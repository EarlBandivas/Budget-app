<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'expense_type',
        'amount',
        'expense_report_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenseReport()
    {
        return $this->belongsTo(ExpenseReport::class);
    }
}


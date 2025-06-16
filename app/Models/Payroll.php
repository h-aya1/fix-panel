<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'employee_id',
        'department',
        'position',
        'name',
        'phone_number',
        'work_days',
        'base_salary',
        'base_salary_str',
        'allowances',
        'allowances_str',
        'gross_pay',
        'gross_pay_str',
        'deductions',
        'deductions_str',
        'net_pay',
        'net_pay_str',
        'remarks',
        'sms_sent_status',
        'allowance_items',
        'deduction_items',
        'sms_details',
        'is_checked',
        'numeric_base_salary',
        'numeric_total_allowances',
        'numeric_total_deductions',
        'numeric_net_pay',
        'payroll_month',
        'payroll_year',
    ];

    protected $casts = [
        'allowance_items' => 'array',
        'deduction_items' => 'array',
        'sms_details' => 'array',
        'is_checked' => 'boolean',
        'work_days' => 'integer',
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'numeric_base_salary' => 'decimal:2',
        'numeric_total_allowances' => 'decimal:2',
        'numeric_total_deductions' => 'decimal:2',
        'numeric_net_pay' => 'decimal:2',
    ];

    // Relationship with Employee if needed
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Helper method to format currency
    public function getFormattedBaseSalaryAttribute()
    {
        return number_format($this->base_salary, 0);
    }

    public function getFormattedAllowancesAttribute()
    {
        return number_format($this->allowances, 0);
    }

    public function getFormattedGrossPayAttribute()
    {
        return number_format($this->gross_pay, 0);
    }

    public function getFormattedDeductionsAttribute()
    {
        return number_format($this->deductions, 0);
    }

    public function getFormattedNetPayAttribute()
    {
        return number_format($this->net_pay, 0);
    }

    // Scope for filtering by month/year
    public function scopeByMonth($query, $month)
    {
        return $query->where('payroll_month', $month);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('payroll_year', $year);
    }

    public function scopeBySmsStatus($query, $status)
    {
        return $query->where('sms_sent_status', $status);
    }
}

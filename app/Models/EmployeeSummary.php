<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSummary extends Model
{
    protected $fillable = [
        'no',
        'employee_id',
        'company_name',
        'position',
        'name',
        'age',
        'resident_registration_number',
        'date_of_joining',
        'contact_number',
        'work_days',
        'base_salary',
        'qualification_allowance',
        'position_allowance',
        'duty_allowance',
        'overtime_allowance',
        'holiday_work_allowance',
        'night_shift_allowance',
        'bonus',
        'adjustment_allowance',
        'transportation_allowance',
        'meal_allowance',
        'labor_day_allowance',
        'paid_leave_allowance',
        'welfare_allowance',
        'other_allowances',
        'total_earnings',
        'health_insurance',
        'long_term_care_insurance',
        'employment_insurance',
        'national_pension',
        'income_tax',
        'local_income_tax',
        'other_deductions',
        'total_deductions',
        'net_payment',
        'remarks',
        'import_batch',
        'imported_at'
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'imported_at' => 'datetime',
        'base_salary' => 'decimal:2',
        'qualification_allowance' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'duty_allowance' => 'decimal:2',
        'overtime_allowance' => 'decimal:2',
        'holiday_work_allowance' => 'decimal:2',
        'night_shift_allowance' => 'decimal:2',
        'bonus' => 'decimal:2',
        'adjustment_allowance' => 'decimal:2',
        'transportation_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'labor_day_allowance' => 'decimal:2',
        'paid_leave_allowance' => 'decimal:2',
        'welfare_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'health_insurance' => 'decimal:2',
        'long_term_care_insurance' => 'decimal:2',
        'employment_insurance' => 'decimal:2',
        'national_pension' => 'decimal:2',
        'income_tax' => 'decimal:2',
        'local_income_tax' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_payment' => 'decimal:2',
    ];
}

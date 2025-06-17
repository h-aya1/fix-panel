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

    /**
     * Calculate employment duration in years and months
     */
    public function getEmploymentDurationAttribute()
    {
        if (!$this->date_of_joining) {
            return null;
        }

        try {
            $startDate = \Carbon\Carbon::parse($this->date_of_joining);
            $endDate = \Carbon\Carbon::now();
            
            // If the start date is in the future, return null
            if ($startDate->isFuture()) {
                return null;
            }
            
            $diff = $startDate->diff($endDate);
            $years = $diff->y;
            $months = $diff->m;
            $days = $diff->d;
            
            if ($years > 0 && $months > 0) {
                return "{$years}y {$months}m";
            } elseif ($years > 0) {
                return "{$years}y";
            } elseif ($months > 0) {
                return "{$months}m";
            } else {
                return "{$days}d";
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Calculate employment duration in days
     */
    public function getEmploymentDurationInDaysAttribute()
    {
        if (!$this->date_of_joining) {
            return null;
        }

        try {
            $startDate = \Carbon\Carbon::parse($this->date_of_joining);
            $endDate = \Carbon\Carbon::now();
            
            // If the start date is in the future, return null
            if ($startDate->isFuture()) {
                return null;
            }

            return $startDate->diffInDays($endDate);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Calculate total earnings and deductions
     */
    public function calculateTotals()
    {
        // Calculate total earnings
        $this->total_earnings = collect([
            $this->base_salary,
            $this->qualification_allowance,
            $this->position_allowance,
            $this->duty_allowance,
            $this->overtime_allowance,
            $this->holiday_work_allowance,
            $this->night_shift_allowance,
            $this->bonus,
            $this->adjustment_allowance,
            $this->transportation_allowance,
            $this->meal_allowance,
            $this->labor_day_allowance,
            $this->paid_leave_allowance,
            $this->welfare_allowance,
            $this->other_allowances,
        ])->filter()->sum();
        
        // Calculate total deductions
        $this->total_deductions = collect([
            $this->health_insurance,
            $this->long_term_care_insurance,
            $this->employment_insurance,
            $this->national_pension,
            $this->income_tax,
            $this->local_income_tax,
            $this->other_deductions,
        ])->filter()->sum();
        
        // Calculate net payment
        $this->net_payment = $this->total_earnings - $this->total_deductions;
    }
}

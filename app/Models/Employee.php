<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'employee_id',
        'work_location',
        'company_name',
        'position',
        'name',
        'age',
        'date_of_birth',
        'ssn',
        'resident_registration_number',
        'join_date',
        'date_of_joining',
        'join_date_str',
        'service_period',
        'employment_duration',
        'work_days',
        'contact',
        'contact_number',
        'base_salary',
        'employment_status_key',
        'employment_status_subtext',
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
        'gender',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_number',
        'bank_name',
        'account_number',
        'account_holder_name',
        'ifsc_code',
        'pan_number',
        'aadhar_number',
        'pf_number',
        'esi_number',
        'uan_number',
        'tax_slab',
        'working_hours',
        'overtime_rate',
        'leave_balance',
        'is_active',
        'notes'
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
}

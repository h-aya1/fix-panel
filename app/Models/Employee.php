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

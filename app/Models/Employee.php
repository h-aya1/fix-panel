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
}

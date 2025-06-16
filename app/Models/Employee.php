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
        'position',
        'name',
        'age',
        'ssn',
        'join_date',
        'join_date_str',
        'service_period',
        'contact',
        'base_salary',
        'employment_status_key',
        'employment_status_subtext',
    ];
}

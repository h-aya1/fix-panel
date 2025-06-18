<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Employee([
            'uid' => \Str::uuid()->toString(),
            'employee_id' => $row['employee_id'] ?? null,
            'work_location' => null, // Not in the new structure
            'company_name' => $row['company_name'] ?? null,
            'position' => $row['position'] ?? null,
            'name' => $row['name'] ?? null,
            'age' => $row['age'] ?? null,
            'date_of_birth' => null, // Not in the new structure
            'ssn' => null, // Not in the new structure
            'resident_registration_number' => $row['resident_registration_number'] ?? null,
            'join_date' => $row['date_of_joining'] ?? null,
            'date_of_joining' => $row['date_of_joining'] ?? null,
            'join_date_str' => null,
            'service_period' => null,
            'employment_duration' => null,
            'work_days' => $row['work_days'] ?? null,
            'contact' => $row['contact_number'] ?? null,
            'contact_number' => $row['contact_number'] ?? null,
            'base_salary' => $row['base_salary'] ?? null,
            'employment_status_key' => 'active',
            'employment_status_subtext' => null,
        ]);
    }
}

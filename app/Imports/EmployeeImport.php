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
            'uid' => $row['uid'] ?? \Str::uuid()->toString(),
            'employee_id' => $row['employee_id'] ?? null,
            'work_location' => $row['work_location'] ?? null,
            'company_name' => $row['company_name'] ?? null,
            'position' => $row['position'] ?? null,
            'name' => $row['name'] ?? null,
            'age' => $row['age'] ?? null,
            'date_of_birth' => $row['date_of_birth'] ?? null,
            'ssn' => $row['ssn'] ?? null,
            'resident_registration_number' => $row['resident_registration_number'] ?? null,
            'join_date' => $row['join_date'] ?? null,
            'date_of_joining' => $row['date_of_joining'] ?? null,
            'join_date_str' => $row['join_date_str'] ?? null,
            'service_period' => $row['service_period'] ?? null,
            'employment_duration' => $row['employment_duration'] ?? null,
            'work_days' => $row['work_days'] ?? null,
            'contact' => $row['contact'] ?? null,
            'contact_number' => $row['contact_number'] ?? null,
            'base_salary' => $row['base_salary'] ?? null,
            'employment_status_key' => $row['employment_status_key'] ?? 'active',
            'employment_status_subtext' => $row['employment_status_subtext'] ?? null,
        ]);
    }
}

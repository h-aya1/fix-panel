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
            'uid' => $row['uid'] ?? uniqid('imp_'),
            'employee_id' => $row['employee_id'] ?? null,
            'work_location' => $row['work_location'] ?? null,
            'position' => $row['position'] ?? null,
            'name' => $row['name'] ?? null,
            'age' => $row['age'] ?? null,
            'ssn' => $row['ssn'] ?? null,
            'join_date' => $row['join_date'] ?? null,
            'join_date_str' => $row['join_date_str'] ?? null,
            'service_period' => $row['service_period'] ?? null,
            'contact' => $row['contact'] ?? null,
            'base_salary' => $row['base_salary'] ?? null,
            'employment_status_key' => $row['employment_status_key'] ?? null,
            'employment_status_subtext' => $row['employment_status_subtext'] ?? null,
        ]);
    }
}

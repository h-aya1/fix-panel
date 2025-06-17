<?php

namespace App\Imports;

use App\Models\EmployeeSummary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeSummaryImport implements ToModel, WithHeadingRow, ToArray
{
    private $importBatch;

    public function __construct()
    {
        $this->importBatch = Str::uuid()->toString();
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['name']) && empty($row['employee_id'])) {
            return null;
        }

        return new EmployeeSummary([
            'no' => $this->parseNumber($row['no'] ?? null),
            'employee_id' => $this->parseString($row['employee_id'] ?? null),
            'company_name' => $this->parseString($row['company_name'] ?? null),
            'position' => $this->parseString($row['position'] ?? null),
            'name' => $this->parseString($row['name'] ?? null),
            'age' => $this->parseNumber($row['age'] ?? null),
            'resident_registration_number' => $this->parseString($row['resident_registration_number'] ?? null),
            'date_of_joining' => $this->parseDate($row['date_of_joining'] ?? null),
            'contact_number' => $this->parseString($row['contact_number'] ?? null),
            'work_days' => $this->parseNumber($row['work_days'] ?? null),
            
            // Salary and Allowances
            'base_salary' => $this->parseDecimal($row['base_salary'] ?? null),
            'qualification_allowance' => $this->parseDecimal($row['qualification_allowance'] ?? null),
            'position_allowance' => $this->parseDecimal($row['position_allowance'] ?? null),
            'duty_allowance' => $this->parseDecimal($row['duty_allowance'] ?? null),
            'overtime_allowance' => $this->parseDecimal($row['overtime_allowance'] ?? null),
            'holiday_work_allowance' => $this->parseDecimal($row['holiday_work_allowance'] ?? null),
            'night_shift_allowance' => $this->parseDecimal($row['night_shift_allowance'] ?? null),
            'bonus' => $this->parseDecimal($row['bonus'] ?? null),
            'adjustment_allowance' => $this->parseDecimal($row['adjustment_allowance'] ?? null),
            'transportation_allowance' => $this->parseDecimal($row['transportation_allowance'] ?? null),
            'meal_allowance' => $this->parseDecimal($row['meal_allowance'] ?? null),
            'labor_day_allowance' => $this->parseDecimal($row['labor_day_allowance'] ?? null),
            'paid_leave_allowance' => $this->parseDecimal($row['paid_leave_allowance'] ?? null),
            'welfare_allowance' => $this->parseDecimal($row['welfare_allowance'] ?? null),
            'other_allowances' => $this->parseDecimal($row['other_allowances'] ?? null),
            'total_earnings' => $this->parseDecimal($row['total_earnings'] ?? null),
            
            // Deductions
            'health_insurance' => $this->parseDecimal($row['health_insurance'] ?? null),
            'long_term_care_insurance' => $this->parseDecimal($row['long_term_care_insurance'] ?? null),
            'employment_insurance' => $this->parseDecimal($row['employment_insurance'] ?? null),
            'national_pension' => $this->parseDecimal($row['national_pension'] ?? null),
            'income_tax' => $this->parseDecimal($row['income_tax'] ?? null),
            'local_income_tax' => $this->parseDecimal($row['local_income_tax'] ?? null),
            'other_deductions' => $this->parseDecimal($row['other_deductions'] ?? null),
            'total_deductions' => $this->parseDecimal($row['total_deductions'] ?? null),
            'net_payment' => $this->parseDecimal($row['net_payment'] ?? null),
            
            'remarks' => $this->parseString($row['remarks'] ?? null),
            'import_batch' => $this->importBatch,
            'imported_at' => now(),
        ]);
    }

    public function getImportBatch()
    {
        return $this->importBatch;
    }

    /**
     * Convert to array for preview functionality.
     */
    public function array(array $array)
    {
        return $array;
    }

    private function parseString($value)
    {
        return $value ? trim((string) $value) : null;
    }

    private function parseNumber($value)
    {
        if (empty($value)) return null;
        return is_numeric($value) ? (int) $value : null;
    }

    private function parseDecimal($value)
    {
        if (empty($value)) return null;
        // Remove commas and convert to decimal
        $cleaned = str_replace([',', ' '], '', $value);
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}

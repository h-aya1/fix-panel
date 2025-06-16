<?php

namespace App\Imports;

use App\Models\Payroll;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class PayrollImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip empty rows
            if (empty(array_filter($row->toArray()))) {
                continue;
            }

            // Calculate values
            $baseSalary = (float) str_replace([',', ' '], '', $row['base_salary'] ?? 0);
            $allowances = (float) str_replace([',', ' '], '', $row['allowances'] ?? 0);
            $deductions = (float) str_replace([',', ' '], '', $row['deductions'] ?? 0);
            $grossPay = $baseSalary + $allowances;
            $netPay = $grossPay - $deductions;

            // Parse allowance and deduction items if they exist
            $allowanceItems = [];
            if (!empty($row['allowance_items'])) {
                $allowanceItems = $this->parseItemsString($row['allowance_items']);
            }

            $deductionItems = [];
            if (!empty($row['deduction_items'])) {
                $deductionItems = $this->parseItemsString($row['deduction_items']);
            }

            Payroll::create([
                'uid' => Str::uuid()->toString(),
                'employee_id' => $row['employee_id'] ?? '',
                'department' => $row['department'] ?? '',
                'position' => $row['position'] ?? '',
                'name' => $row['name'] ?? '',
                'phone_number' => $row['phone_number'] ?? null,
                'work_days' => (int) ($row['work_days'] ?? 0),
                'base_salary' => $baseSalary,
                'base_salary_str' => number_format($baseSalary, 0),
                'allowances' => $allowances,
                'allowances_str' => number_format($allowances, 0),
                'gross_pay' => $grossPay,
                'gross_pay_str' => number_format($grossPay, 0),
                'deductions' => $deductions,
                'deductions_str' => number_format($deductions, 0),
                'net_pay' => $netPay,
                'net_pay_str' => number_format($netPay, 0),
                'remarks' => $row['remarks'] ?? null,
                'sms_sent_status' => 'pending',
                'allowance_items' => $allowanceItems,
                'deduction_items' => $deductionItems,
                'is_checked' => true,
                'numeric_base_salary' => $baseSalary,
                'numeric_total_allowances' => $allowances,
                'numeric_total_deductions' => $deductions,
                'numeric_net_pay' => $netPay,
                'payroll_month' => $row['payroll_month'] ?? null,
                'payroll_year' => $row['payroll_year'] ?? date('Y'),
            ]);
        }
    }

    /**
     * Parse items string into array format
     * Expected format: "item1:value1,item2:value2"
     */
    private function parseItemsString($itemsString)
    {
        $items = [];
        if (empty($itemsString)) {
            return $items;
        }

        $pairs = explode(',', $itemsString);
        foreach ($pairs as $pair) {
            $parts = explode(':', trim($pair));
            if (count($parts) === 2) {
                $items[] = [
                    'label_key' => trim($parts[0]),
                    'label_translation' => trim($parts[0]), // You might want to translate this
                    'value' => (float) str_replace([',', ' '], '', trim($parts[1]))
                ];
            }
        }

        return $items;
    }

    /**
     * Validation rules for the import
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'required|string',
            'department' => 'required|string',
            'position' => 'required|string',
            'name' => 'required|string',
            'work_days' => 'nullable|numeric|min:0',
            'base_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            'employee_id.required' => 'Employee ID is required',
            'department.required' => 'Department is required',
            'position.required' => 'Position is required',
            'name.required' => 'Name is required',
            'base_salary.required' => 'Base salary is required',
            'base_salary.numeric' => 'Base salary must be a number',
        ];
    }
}

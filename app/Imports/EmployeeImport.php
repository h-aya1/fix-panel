<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EmployeeImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, SkipsOnError
{
    use SkipsErrors;

    private $rowCount = 0;
    private $importedCount = 0;
    private $skippedCount = 0;
    private $updatedCount = 0;
    private $importErrors = []; // Renamed from $errors to avoid conflict with SkipsErrors trait
    private $headers = [];

    public function __construct()
    {
        $this->headers = [
            'employee_id' => ['employee id', 'employee_id', 'emp_id', 'id', 'employee no'],
            'work_location' => ['work location', 'location', 'work_location'],
            'position' => ['position', 'job title', 'designation'],
            'name' => ['name', 'employee name', 'full name', 'employee_name'],
            'age' => ['age'],
            'resident_registration_number' => ['resident registration number', 'rrn', 'registration number', 'resident_id'],
            'join_date' => ['date of joining', 'join date', 'joining date', 'hire date', 'start date', 'employment date'],
            'contact_number' => ['contact number', 'phone', 'mobile', 'phone number', 'contact'],
            'work_days' => ['work days', 'working days', 'days worked'],
            'base_salary' => ['base salary', 'basic salary', 'salary', 'basic pay'],
            'qualification_allowance' => ['qualification allowance', 'qualification', 'education allowance'],
            'position_allowance' => ['position allowance', 'job grade allowance'],
            'duty_allowance' => ['duty allowance', 'responsibility allowance'],
            'overtime_allowance' => ['overtime allowance', 'ot allowance', 'overtime pay'],
            'holiday_work_allowance' => ['holiday work allowance', 'holiday pay'],
            'night_shift_allowance' => ['night shift allowance', 'night shift pay', 'night differential'],
            'bonus' => ['bonus', 'incentive', 'performance bonus'],
            'adjustment_allowance' => ['adjustment allowance', 'adjustment', 'special allowance'],
            'transportation_allowance' => ['transportation allowance', 'transport allowance', 'commute allowance'],
            'meal_allowance' => ['meal allowance', 'food allowance', 'lunch allowance'],
            'labor_day_allowance' => ['labor day allowance', 'labor day pay'],
            'paid_leave_allowance' => ['paid leave allowance', 'leave encashment'],
            'welfare_allowance' => ['welfare allowance', 'welfare benefits'],
            'other_allowances' => ['other allowances', 'miscellaneous allowances', 'additional allowances'],
            'total_earnings' => ['total earnings', 'gross earnings', 'total pay'],
            'health_insurance' => ['health insurance', 'medical insurance'],
            'long_term_care_insurance' => ['long-term care insurance', 'ltc insurance', 'elderly care insurance'],
            'employment_insurance' => ['employment insurance', 'ei', 'unemployment insurance'],
            'national_pension' => ['national pension', 'pension contribution', 'npf'],
            'income_tax' => ['income tax', 'tax', 'withholding tax'],
            'local_income_tax' => ['local income tax', 'local tax', 'resident tax'],
            'other_deductions' => ['other deductions', 'miscellaneous deductions'],
            'total_deductions' => ['total deductions', 'total deductions'],
            'net_payment' => ['net payment', 'net pay', 'take home pay'],
            'remarks' => ['remarks', 'notes', 'comments']
        ];
    }

    public function model(array $row)
    {
        // Skip empty rows
        if ($this->isEmptyRow($row)) {
            $this->skippedCount++;
            \Log::debug('Skipping empty row', ['row' => $this->rowCount + 1]);
            return null;
        }

        $this->rowCount++;
        \Log::debug('Processing row', ['row' => $this->rowCount, 'data' => $row]);
        
        try {
            // Convert all row keys to lowercase for case-insensitive matching
            $row = array_change_key_case($row, CASE_LOWER);
            
            // Map headers to standard field names
            $mappedRow = [];
            foreach ($this->headers as $standardField => $possibleHeaders) {
                foreach ($possibleHeaders as $header) {
                    $header = strtolower(trim($header));
                    if (isset($row[$header]) && !empty($row[$header])) {
                        $mappedRow[$standardField] = $row[$header];
                        break;
                    }
                }
            }
            
            // If no name is found, try to find it using common name fields
            if (empty($mappedRow['name'])) {
                $nameFields = ['employee name', 'name', 'full name', 'employee_name', 'full_name'];
                foreach ($nameFields as $field) {
                    if (!empty($row[$field])) {
                        $mappedRow['name'] = $row[$field];
                        break;
                    }
                }
            }
            
            // Skip rows without a name
            if (empty($mappedRow['name'])) {
                $errorMsg = 'Employee name is required';
                $this->importErrors[] = [
                    'row' => $this->rowCount,
                    'message' => $errorMsg,
                    'data' => $row
                ];
                \Log::warning('Skipping row - missing name', ['row' => $this->rowCount]);
                return null;
            }

            // Clean and format data
            $employeeData = [
                'uid' => (string) Str::uuid(),
                'employee_id' => $this->cleanValue($mappedRow['employee_id'] ?? null, true),
                'work_location' => $this->cleanValue($mappedRow['work_location'] ?? null),
                'position' => $this->cleanValue($mappedRow['position'] ?? 'Employee'),
                'name' => $this->cleanValue($mappedRow['name'] ?? null),
                'age' => $this->parseInteger($mappedRow['age'] ?? null),
                'resident_registration_number' => $this->cleanValue($mappedRow['resident_registration_number'] ?? null),
                'join_date' => $this->parseDate($mappedRow['join_date'] ?? null),
                'date_of_joining' => $this->parseDate($mappedRow['join_date'] ?? null),
                'contact_number' => $this->cleanPhoneNumber($mappedRow['contact_number'] ?? null),
                'work_days' => $this->parseInteger($mappedRow['work_days'] ?? 0),
                'base_salary' => $this->parseFloat($mappedRow['base_salary'] ?? 0),
                'qualification_allowance' => $this->parseFloat($mappedRow['qualification_allowance'] ?? 0),
                'position_allowance' => $this->parseFloat($mappedRow['position_allowance'] ?? 0),
                'duty_allowance' => $this->parseFloat($mappedRow['duty_allowance'] ?? 0),
                'overtime_allowance' => $this->parseFloat($mappedRow['overtime_allowance'] ?? 0),
                'holiday_work_allowance' => $this->parseFloat($mappedRow['holiday_work_allowance'] ?? 0),
                'night_shift_allowance' => $this->parseFloat($mappedRow['night_shift_allowance'] ?? 0),
                'bonus' => $this->parseFloat($mappedRow['bonus'] ?? 0),
                'adjustment_allowance' => $this->parseFloat($mappedRow['adjustment_allowance'] ?? 0),
                'transportation_allowance' => $this->parseFloat($mappedRow['transportation_allowance'] ?? 0),
                'meal_allowance' => $this->parseFloat($mappedRow['meal_allowance'] ?? 0),
                'labor_day_allowance' => $this->parseFloat($mappedRow['labor_day_allowance'] ?? 0),
                'paid_leave_allowance' => $this->parseFloat($mappedRow['paid_leave_allowance'] ?? 0),
                'welfare_allowance' => $this->parseFloat($mappedRow['welfare_allowance'] ?? 0),
                'other_allowances' => $this->parseFloat($mappedRow['other_allowances'] ?? 0),
                'total_earnings' => $this->parseFloat($mappedRow['total_earnings'] ?? 0),
                'health_insurance' => $this->parseFloat($mappedRow['health_insurance'] ?? 0),
                'long_term_care_insurance' => $this->parseFloat($mappedRow['long_term_care_insurance'] ?? 0),
                'employment_insurance' => $this->parseFloat($mappedRow['employment_insurance'] ?? 0),
                'national_pension' => $this->parseFloat($mappedRow['national_pension'] ?? 0),
                'income_tax' => $this->parseFloat($mappedRow['income_tax'] ?? 0),
                'local_income_tax' => $this->parseFloat($mappedRow['local_income_tax'] ?? 0),
                'other_deductions' => $this->parseFloat($mappedRow['other_deductions'] ?? 0),
                'total_deductions' => $this->parseFloat($mappedRow['total_deductions'] ?? 0),
                'net_payment' => $this->parseFloat($mappedRow['net_payment'] ?? 0),
                'remarks' => $this->cleanValue($mappedRow['remarks'] ?? null),
                'employment_status_key' => 'active',
                'gender' => $this->parseGender($row['gender'] ?? $row['sex'] ?? null),
                'email' => $this->cleanEmail($mappedRow['email'] ?? null),
                'address' => $this->cleanValue($row['address'] ?? $row['residence'] ?? null),
                'city' => $this->cleanValue($row['city'] ?? null),
                'state' => $this->cleanValue($row['state'] ?? $row['province'] ?? null),
                'postal_code' => $this->cleanValue($row['postal_code'] ?? $row['zip'] ?? null),
                'country' => $this->cleanValue($row['country'] ?? null),
                'emergency_contact_name' => $this->cleanValue($row['emergency_contact'] ?? $row['emergency_contact_name'] ?? null),
                'emergency_contact_number' => $this->cleanPhoneNumber($row['emergency_contact_number'] ?? $row['emergency_phone'] ?? null),
                'bank_name' => $this->cleanValue($row['bank_name'] ?? $row['bank'] ?? null),
                'account_number' => $this->cleanValue($row['account_number'] ?? $row['bank_account'] ?? null),
                'account_holder_name' => $this->cleanValue($row['account_holder_name'] ?? $row['account_name'] ?? null),
                'ifsc_code' => $this->cleanValue($row['ifsc_code'] ?? $row['bank_code'] ?? null),
                'pan_number' => $this->cleanValue($row['pan_number'] ?? $row['pan'] ?? null),
                'aadhar_number' => $this->cleanValue($row['aadhar_number'] ?? $row['aadhar'] ?? null),
                'pf_number' => $this->cleanValue($row['pf_number'] ?? $row['provident_fund'] ?? null),
                'esi_number' => $this->cleanValue($row['esi_number'] ?? $row['esi'] ?? null),
                'uan_number' => $this->cleanValue($row['uan_number'] ?? $row['uan'] ?? null),
                'tax_slab' => $this->cleanValue($row['tax_slab'] ?? $row['tax_bracket'] ?? null),
                'working_hours' => $this->parseFloat($row['working_hours'] ?? 8.0),
                'overtime_rate' => $this->parseFloat($row['overtime_rate'] ?? 1.5),
                'leave_balance' => $this->parseFloat($row['leave_balance'] ?? 0),
                'is_active' => $this->parseBoolean($row['is_active'] ?? $row['active'] ?? true),
                'notes' => $this->cleanValue($row['notes'] ?? $row['remarks'] ?? null),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Generate a unique employee ID if not provided
            if (empty($employeeData['employee_id'])) {
                $employeeData['employee_id'] = 'EMP' . strtoupper(Str::random(8));
            }

            // Check if employee with this ID already exists
            $existingEmployee = null;
            if (!empty($employeeData['employee_id'])) {
                $existingEmployee = Employee::where('employee_id', $employeeData['employee_id'])->first();
                if ($existingEmployee) {
                    \Log::debug('Found existing employee by ID', [
                        'employee_id' => $employeeData['employee_id'],
                        'name' => $employeeData['name']
                    ]);
                }
            }
            
            // If no employee with this ID, check by name and birth date as fallback
            if (!$existingEmployee && !empty($employeeData['name'])) {
                $query = Employee::where('name', $employeeData['name']);
                
                if (!empty($employeeData['date_of_birth'])) {
                    $query->whereDate('date_of_birth', $employeeData['date_of_birth']);
                }
                
                $existingEmployee = $query->first();
                
                if ($existingEmployee) {
                    \Log::debug('Found existing employee by name', [
                        'name' => $employeeData['name'],
                        'date_of_birth' => $employeeData['date_of_birth'] ?? 'not provided'
                    ]);
                }
            }

            if ($existingEmployee) {
                // Update existing employee
                \Log::info('Updating existing employee', [
                    'id' => $existingEmployee->id,
                    'employee_id' => $existingEmployee->employee_id,
                    'name' => $existingEmployee->name
                ]);
                
                $existingEmployee->update($employeeData);
                $this->updatedCount++;
                \Log::debug('Employee updated successfully', ['id' => $existingEmployee->id]);
                return null; // Returning null skips creating a new model
            } else {
                // Create new employee
                \Log::info('Creating new employee', [
                    'employee_id' => $employeeData['employee_id'] ?? 'auto-generated',
                    'name' => $employeeData['name']
                ]);
                
                $employee = new Employee($employeeData);
                $employee->save();
                $this->importedCount++;
                
                \Log::debug('Employee created successfully', ['id' => $employee->id]);
                return $employee;
            }
            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->importErrors[] = [
                'row' => $this->rowCount,
                'message' => $e->getMessage(),
                'data' => $row,
                'trace' => $e->getTraceAsString()
            ];
            
            Log::error('Error importing employee row', [
                'row' => $this->rowCount,
                'error' => $errorMessage,
                'data' => $row,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Skip the row and continue with the next one
            return null;
        }
    }


    public function rules(): array
    {
        return [
            // Use wildcard validation for any possible column names
            '*.employee_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        $stringValue = $this->cleanValue($value, true);
                        if (strlen($stringValue) > 100) {
                            $fail('The employee_id field must not exceed 100 characters.');
                        }
                    }
                }
            ],
            '*.name' => [
                function ($attribute, $value, $fail) {
                    if (empty($value) || trim($value) === '') {
                        $fail('Employee name is required.');
                    }
                }
            ],
            '*.employee name' => [
                function ($attribute, $value, $fail) {
                    if (empty($value) || trim($value) === '') {
                        $fail('Employee name is required.');
                    }
                }
            ],
            '*.full name' => [
                function ($attribute, $value, $fail) {
                    if (empty($value) || trim($value) === '') {
                        $fail('Employee name is required.');
                    }
                }
            ],
            '*.employee_name' => [
                function ($attribute, $value, $fail) {
                    if (empty($value) || trim($value) === '') {
                        $fail('Employee name is required.');
                    }
                }
            ],
            '*.date of joining' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        $parsedDate = $this->parseDate($value);
                        if ($parsedDate === null) {
                            $fail('Invalid date format for joining date. Use YYYY-MM-DD format');
                        }
                    }
                }
            ],
            '*.join date' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        $parsedDate = $this->parseDate($value);
                        if ($parsedDate === null) {
                            $fail('Invalid date format for joining date. Use YYYY-MM-DD format');
                        }
                    }
                }
            ],
            '*.joining date' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '') {
                        $parsedDate = $this->parseDate($value);
                        if ($parsedDate === null) {
                            $fail('Invalid date format for joining date. Use YYYY-MM-DD format');
                        }
                    }
                }
            ],
            '*.base salary' => 'nullable|numeric|min:0',
            '*.basic salary' => 'nullable|numeric|min:0',
            '*.salary' => 'nullable|numeric|min:0',
            '*.work days' => 'nullable|integer|min:0|max:31',
            '*.working days' => 'nullable|integer|min:0|max:31',
            '*.age' => 'nullable|integer|min:16|max:100',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.employee_id' => 'The employee_id field must be a string and not exceed 100 characters.',
            '*.name' => 'Employee name is required',
            '*.employee name' => 'Employee name is required',
            '*.full name' => 'Employee name is required', 
            '*.employee_name' => 'Employee name is required',
            '*.date of joining' => 'Invalid date format for joining date. Use YYYY-MM-DD format',
            '*.join date' => 'Invalid date format for joining date. Use YYYY-MM-DD format',
            '*.joining date' => 'Invalid date format for joining date. Use YYYY-MM-DD format',
            '*.base salary.numeric' => 'Base salary must be a number',
            '*.base salary.min' => 'Base salary cannot be negative',
            '*.basic salary.numeric' => 'Basic salary must be a number',
            '*.basic salary.min' => 'Basic salary cannot be negative',
            '*.salary.numeric' => 'Salary must be a number',
            '*.salary.min' => 'Salary cannot be negative',
            '*.work days.integer' => 'Work days must be a whole number',
            '*.work days.min' => 'Work days cannot be negative',
            '*.work days.max' => 'Work days cannot be more than 31',
            '*.working days.integer' => 'Working days must be a whole number',
            '*.working days.min' => 'Working days cannot be negative',
            '*.working days.max' => 'Working days cannot be more than 31',
            '*.age.min' => 'Employee must be at least 16 years old',
            '*.age.max' => 'Employee age cannot be more than 100',
        ];
    }

    public function batchSize(): int
    {
        return 100; // Process 100 records at a time for better performance
    }

    public function chunkSize(): int
    {
        return 100; // Process in chunks of 100 for better memory management
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors()
    {
        return $this->importErrors;
    }

    private function cleanValue($value, $isEmployeeId = false)
    {
        if (is_null($value) || $value === '') {
            return $isEmployeeId ? '' : null;
        }
        
        // For employee_id, ensure it's treated as a string and preserve leading zeros
        if ($isEmployeeId) {
            // Convert to string first
            $stringValue = (string)$value;
            
            // Handle scientific notation (e.g., 1.23E+5)
            if (is_numeric($value) && (strpos($stringValue, 'E') !== false || strpos($stringValue, 'e') !== false)) {
                $stringValue = number_format($value, 0, '', '');
            }
            
            // Remove decimal part if it exists (for cases like 547.0)
            if (strpos($stringValue, '.') !== false && is_numeric($stringValue)) {
                $stringValue = substr($stringValue, 0, strpos($stringValue, '.'));
            }
            
            // Trim whitespace
            $stringValue = trim($stringValue);
            
            return $stringValue;
        }
        
        // For other values
        if (is_numeric($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $value = trim($value);
            return $value === '' ? null : $value;
        }
        
        return $value;
    }
    
    private function isEmptyRow($row)
    {
        if (!is_array($row)) {
            return true;
        }
        
        // Check if all values in the row are empty
        foreach ($row as $value) {
            if (!empty($value) && trim($value) !== '') {
                return false;
            }
        }
        
        return true;
    }

    private function cleanPhoneNumber($phone)
    {
        if (empty($phone)) return null;
        
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it's a valid Korean phone number (10 or 11 digits starting with 01)
        if (preg_match('/^01[0-9]{8,9}$/', $phone)) {
            // Format as 010-1234-5678 or 010-123-4567
            return preg_replace('/(\d{3})(\d{3,4})(\d{4})/', '$1-$2-$3', $phone);
        }
        
        return $phone;
    }

    private function parseInteger($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (is_string($value)) {
            // Remove non-numeric characters except minus sign
            $value = preg_replace('/[^0-9-]/', '', $value);
            return $value === '' ? null : (int) $value;
        }
        return null;
    }

    private function parseFloat($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        if (is_string($value)) {
            // Replace comma with dot for decimal point and remove any other non-numeric characters except minus sign and dot
            $value = str_replace(',', '.', $value);
            $value = preg_replace('/[^0-9.-]/', '', $value);
            return $value === '' ? null : (float) $value;
        }
        return null;
    }

    private function parseDate($value)
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return null;
        }

        try {
            // If it's already a valid date string in Y-m-d format
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value;
            }

            // Handle Excel timestamp (days since 1900-01-01)
            if (is_numeric($value) && $value > 0) {
                try {
                    // First try PhpSpreadsheet's date conversion if available
                    if (class_exists('\PhpOffice\PhpSpreadsheet\Shared\Date')) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                        if ($date) {
                            return $date->format('Y-m-d');
                        }
                    }
                    
                    // Fallback: Manual conversion for Excel serial dates
                    // Excel's epoch is 1900-01-01, but it incorrectly considers 1900 as a leap year
                    $utcDays = (int)$value;
                    $timestamp = ($utcDays - 25569) * 86400; // 25569 = days between 1970-01-01 and 1900-01-01
                    $date = new \DateTime('@' . $timestamp);
                    return $date->format('Y-m-d');
                } catch (\Exception $e) {
                    \Log::warning('Excel date conversion error', [
                        'value' => $value,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Handle YYYYMMDD format (as number or string)
            if (is_numeric($value) || ctype_digit($value)) {
                $value = (string)$value;
                if (strlen($value) === 8) {
                    $date = \DateTime::createFromFormat('Ymd', $value);
                    if ($date && $date->format('Ymd') === $value) {
                        return $date->format('Y-m-d');
                    }
                }
            }

            if (is_string($value)) {
                // Clean up the date string
                $value = trim($value);
                
                // Try to parse various date formats
                $formats = [
                    'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d',
                    'd-m-Y', 'm-d-Y', 'Y.m.d', 'd.m.Y', 'm.d.Y',
                    'd M Y', 'j M Y', 'd F Y', 'j F Y', // 01 Jan 2023, 1 Jan 2023
                    'M d, Y', 'F d, Y', // Jan 01, 2023, January 01, 2023
                ];

                foreach ($formats as $format) {
                    $date = \DateTime::createFromFormat($format, $value);
                    if ($date && $date->format($format) === $value) {
                        return $date->format('Y-m-d');
                    }
                }

                // Try strtotime as a fallback with more strict checking
                if (($timestamp = strtotime($value)) !== false) {
                    $parsedDate = date('Y-m-d', $timestamp);
                    // Additional validation to prevent wrong date parsing
                    $checkDate = \DateTime::createFromFormat('Y-m-d', $parsedDate);
                    if ($checkDate && $checkDate->format('Y-m-d') === $parsedDate) {
                        return $parsedDate;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Date parsing error', [
                'value' => $value,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    private function parseGender($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = strtolower(trim($value));
        
        // Handle common gender representations
        $malePatterns = ['male', 'm', 'man', 'boy', '남', '남성', '남자'];
        $femalePatterns = ['female', 'f', 'woman', 'girl', '여', '여성', '여자'];
        $otherPatterns = ['other', 'o', 'non-binary', 'nonbinary', 'nb', 'x', 'prefer not to say', '기타', 'etc'];
        
        if (in_array($value, $malePatterns)) {
            return 'male';
        }
        
        if (in_array($value, $femalePatterns)) {
            return 'female';
        }
        
        if (in_array($value, $otherPatterns)) {
            return 'other';
        }
        
        // Handle numeric values that might come from Excel (1 = male, 2 = female, etc.)
        if (is_numeric($value)) {
            $value = (int)$value;
            if ($value === 1) return 'male';
            if ($value === 2) return 'female';
            return 'other';
        }
        
        // Default to null if not recognized
        return null;
    }
    
    /**
     * Clean and validate an email address
     * 
     * @param string|null $email
     * @return string|null
     */
    private function cleanEmail($email)
    {
        if (empty($email) || !is_string($email)) {
            return null;
        }
        
        // Trim and convert to lowercase
        $email = strtolower(trim($email));
        
        // Remove any surrounding whitespace, quotes, or angle brackets
        $email = trim($email, " \t\n\r\0\x0B<>\"'`");
        
        // Simple email validation using filter_var
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        
        // Additional validation for common email patterns
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            return null;
        }
        
        // Check for common disposable email domains
        $disposableDomains = [
            'tempmail', 'mailinator', 'guerrillamail', '10minutemail', 'yopmail', 'throwawaymail',
            'dispostable', 'maildrop', 'getnada', 'temp-mail', 'tempmailaddress', 'fakeinbox',
            'mailnesia', 'getairmail', 'mailcatch', 'tempr', 'tempmailer', 'temporarymail', 'tmpmail'
        ];
        
        $domain = substr(strrchr($email, "@"), 1);
        
        foreach ($disposableDomains as $disposable) {
            if (strpos($domain, $disposable) !== false) {
                return null;
            }
        }
        
        return $email;
    }
    
    /**
     * Parse various boolean representations into a boolean value
     * 
     * @param mixed $value
     * @return bool
     */
    private function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_numeric($value)) {
            return (int)$value !== 0;
        }
        
        if (is_string($value)) {
            $value = strtolower(trim($value));
            
            $trueValues = ['true', 'yes', 'y', '1', 'active', 'enabled', 'on', 't'];
            $falseValues = ['false', 'no', 'n', '0', 'inactive', 'disabled', 'off', 'f', ''];
            
            if (in_array($value, $trueValues, true)) {
                return true;
            }
            
            if (in_array($value, $falseValues, true)) {
                return false;
            }
            
            // For Korean language support
            if (in_array($value, ['예', '네', '활성', '사용', '켜짐'], true)) {
                return true;
            }
            
            if (in_array($value, ['아니오', '비활성', '사용안함', '꺼짐'], true)) {
                return false;
            }
        }
        
        // Default to true for any other non-empty value
        return !empty($value);
    }
}

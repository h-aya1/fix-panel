<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // Get unique company names for filter
        $companyNames = Employee::query()
            ->select('work_location')
            ->distinct()
            ->whereNotNull('work_location')
            ->where('work_location', '!=', '')
            ->orderBy('work_location')
            ->pluck('work_location');

        if ($request->ajax()) {
            $query = Employee::query();
            if ($request->has('company') && $request->company) {
                $query->where('work_location', $request->company);
            }
            $employees = $query->get();
            return response()->json($employees);
        }
        $employees = Employee::all();
        return view('employees.index', compact('employees', 'companyNames'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:employees,employee_id',
            'work_location' => 'nullable|string',
            'company_name' => 'nullable|string',
            'position' => 'required|string',
            'name' => 'required|string',
            'age' => 'nullable|integer',
            'ssn' => 'nullable|string',
            'resident_registration_number' => 'nullable|string',
            'date_of_joining' => 'nullable|date',
            'service_period' => 'nullable|string',
            'employment_duration' => 'nullable|string',
            'work_days' => 'nullable|integer',
            'contact' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'base_salary' => 'nullable|numeric',
            'employment_status_key' => 'required|string',
            'employment_status_subtext' => 'nullable|string',
        ]);
        $validated['uid'] = Str::uuid()->toString();
        $validated['date_of_joining'] = $validated['date_of_joining'] ? Carbon::parse($validated['date_of_joining']) : null;
        $validated['work_location'] = $validated['company_name'] ?? null;
        Employee::create($validated);
        Session::flash('success', __('employee.created_successfully'));
        return redirect()->route('employees.index');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'uid' => 'required|string|unique:employees,uid,' . $employee->id,
            'employee_id' => 'required|string|unique:employees,employee_id,' . $employee->id,
            'work_location' => 'nullable|string',
            'company_name' => 'nullable|string',
            'position' => 'required|string',
            'name' => 'required|string',
            'age' => 'nullable|integer',
            'date_of_birth' => 'nullable|date',
            'ssn' => 'nullable|string',
            'resident_registration_number' => 'nullable|string',
            'join_date' => 'nullable|date',
            'date_of_joining' => 'nullable|date',
            'join_date_str' => 'nullable|string',
            'service_period' => 'nullable|string',
            'employment_duration' => 'nullable|string',
            'work_days' => 'nullable|integer',
            'contact' => 'nullable|string',
            'contact_number' => 'nullable|string',
            'base_salary' => 'nullable|numeric',
            'employment_status_key' => 'required|string',
            'employment_status_subtext' => 'nullable|string',
        ]);
        $validated['join_date'] = $validated['join_date'] ? Carbon::parse($validated['join_date']) : null;
        $validated['date_of_joining'] = $validated['date_of_joining'] ? Carbon::parse($validated['date_of_joining']) : null;
        $validated['date_of_birth'] = $validated['date_of_birth'] ? Carbon::parse($validated['date_of_birth']) : null;
        $employee->update($validated);
        Session::flash('success', __('employee.updated_successfully'));
        return redirect()->route('employees.index');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        Session::flash('success', __('employee.deleted_successfully'));
        return redirect()->route('employees.index');
    }

    public function importForm()
    {
        return view('employees.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);
        Excel::import(new \App\Imports\EmployeeImport, $request->file('file'));
        Session::flash('success', __('employee.imported_successfully'));
        return redirect()->route('employees.index');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        try {
            $file = $request->file('file');
            
            Log::info('Starting file preview', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);
            
            // Read the raw data
            $data = Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found in file'
                ]);
            }

            $allData = $data[0];
            Log::info('Raw data structure', [
                'total_rows' => count($allData),
                'first_few_rows' => array_slice($allData, 0, 5)
            ]);
            
            // Find the header row automatically
            $headerRowIndex = $this->findHeaderRow($allData);
            
            if ($headerRowIndex === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not find header row. Please ensure your file has proper column headers.'
                ]);
            }

            // Get headers and clean them
            $headers = $allData[$headerRowIndex];
            $cleanHeaders = $this->cleanHeaders($headers);
            
            // Data starts after header row
            $dataStartIndex = $headerRowIndex + 1;
            $dataRows = array_slice($allData, $dataStartIndex);
            
            Log::info('Header processing', [
                'header_row_index' => $headerRowIndex,
                'original_headers' => $headers,
                'clean_headers' => $cleanHeaders,
                'data_rows_count' => count($dataRows)
            ]);
            
            if (empty($dataRows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data rows found after header'
                ]);
            }

            // Process rows with improved mapping
            $processedRows = [];
            foreach ($dataRows as $rowIndex => $row) {
                // Skip completely empty rows
                if ($this->isEmptyRow($row)) {
                    continue;
                }
                
                $processedRow = $this->processRow($row, $cleanHeaders, $rowIndex);
                
                // Only add rows that have essential data
                if ($this->hasEssentialData($processedRow)) {
                    $processedRows[] = $processedRow;
                }
            }
            
            Log::info('Processing completed', [
                'processed_rows_count' => count($processedRows),
                'sample_processed_row' => $processedRows[0] ?? null
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'headers' => $headers,
                    'clean_headers' => $cleanHeaders,
                    'rows' => $processedRows,
                    'total' => count($processedRows),
                    'header_row_index' => $headerRowIndex,
                    'data_start_index' => $dataStartIndex
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Preview error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Import error: ' . $e->getMessage()
            ]);
        }
    }

    private function findHeaderRow($allData)
    {
        $headerKeywords = ['employee', 'name', 'position', 'company', 'id', 'contact', 'salary'];
        
        for ($i = 0; $i < min(5, count($allData)); $i++) {
            $row = $allData[$i];
            if (!is_array($row)) continue;
            
            $rowString = strtolower(implode('|', array_map('strval', $row)));
            
            // Count how many header keywords we find
            $keywordCount = 0;
            foreach ($headerKeywords as $keyword) {
                if (strpos($rowString, $keyword) !== false) {
                    $keywordCount++;
                }
            }
            
            // If we find at least 2 keywords, likely a header row
            if ($keywordCount >= 2) {
                return $i;
            }
        }
        
        return false;
    }

    private function cleanHeaders($headers)
    {
        $cleanHeaders = [];
        foreach ($headers as $header) {
            $clean = trim(strtolower(str_replace([' ', '-', '_'], '_', $header)));
            $clean = preg_replace('/[^a-z0-9_]/', '', $clean);
            $cleanHeaders[] = $clean;
        }
        return $cleanHeaders;
    }

    private function isEmptyRow($row)
    {
        if (!is_array($row)) return true;
        
        foreach ($row as $cell) {
            if (!empty(trim($cell))) {
                return false;
            }
        }
        return true;
    }

    private function processRow($row, $cleanHeaders, $rowIndex)
    {
        $processedRow = [
            'row_index' => $rowIndex,
            'selected' => true
        ];
        
        // Map each cell to the corresponding field
        for ($i = 0; $i < count($row); $i++) {
            if (isset($cleanHeaders[$i])) {
                $fieldName = $this->mapFieldName($cleanHeaders[$i]);
                $cellValue = $this->cleanCellValue($row[$i]);
                $processedRow[$fieldName] = $cellValue;
            }
        }
        
        return $processedRow;
    }

    private function hasEssentialData($row)
    {
        return !empty($row['employee_id']) || !empty($row['name']);
    }

    private function mapFieldName($cleanHeader)
    {
        $fieldMapping = [
            'employee_id' => 'employee_id',
            'employeeid' => 'employee_id',
            'emp_id' => 'employee_id',
            'id' => 'employee_id',
            'staff_id' => 'employee_id',
            'worker_id' => 'employee_id',
            
            'name' => 'name',
            'employee_name' => 'name',
            'full_name' => 'name',
            'staff_name' => 'name',
            'worker_name' => 'name',
            

            'company_name' => 'company_name',
            'company' => 'company_name',
            'organization' => 'company_name',
            'workplace' => 'company_name',
            'work_location' => 'company_name',
            
            'position' => 'position',
            'job_title' => 'position',
            'title' => 'position',
            'role' => 'position',
            'designation' => 'position',
            
            'date_of_birth' => 'date_of_birth',
            'birth_date' => 'date_of_birth',
            'dob' => 'date_of_birth',
            'birthday' => 'date_of_birth',
            
            'resident_registration_number' => 'resident_registration_number',
            'registration_number' => 'resident_registration_number',
            'rrn' => 'resident_registration_number',
            'national_id' => 'resident_registration_number',
            
            'contact_number' => 'contact_number',
            'phone' => 'contact_number',
            'mobile' => 'contact_number',
            'telephone' => 'contact_number',
            'phone_number' => 'contact_number',
            'mobile_number' => 'contact_number',
            
            'date_of_joining' => 'date_of_joining',
            'joining_date' => 'date_of_joining',
            'join_date' => 'date_of_joining',
            'start_date' => 'date_of_joining',
            'hire_date' => 'date_of_joining',
            
            'employment_duration' => 'employment_duration',
            'duration' => 'employment_duration',
            'service_period' => 'employment_duration',
            'tenure' => 'employment_duration',
            
            'work_days' => 'work_days',
            'working_days' => 'work_days',
            'days' => 'work_days',
            'total_days' => 'work_days',
            
            'base_salary' => 'base_salary',
            'salary' => 'base_salary',
            'wage' => 'base_salary',
            'pay' => 'base_salary',
            'income' => 'base_salary',
            
            'age' => 'age',
            'years_old' => 'age',
            'employee_age' => 'age'
        ];

        return $fieldMapping[$cleanHeader] ?? $cleanHeader;
    }

    private function cleanCellValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        $cleaned = trim(strval($value));
        return $cleaned === '' ? null : $cleaned;
    }

    public function savePreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'selected_rows' => 'required|array',
        ]);

        try {
            $file = $request->file('file');
            $selectedRows = $request->input('selected_rows');
            
            Log::info('Starting save preview', [
                'selected_rows' => $selectedRows,
                'selected_count' => count($selectedRows)
            ]);
            
            // Re-process the file to get the same data structure as preview
            $data = Excel::toArray([], $file);
            $allData = $data[0];
            
            $headerRowIndex = $this->findHeaderRow($allData);
            $headers = $allData[$headerRowIndex];
            $cleanHeaders = $this->cleanHeaders($headers);
            
            $dataStartIndex = $headerRowIndex + 1;
            $dataRows = array_slice($allData, $dataStartIndex);
            
            Log::info('Save preview data structure', [
                'header_row_index' => $headerRowIndex,
                'data_start_index' => $dataStartIndex,
                'total_data_rows' => count($dataRows),
                'clean_headers' => $cleanHeaders
            ]);
            
            $importedCount = 0;
            $errors = [];

            foreach ($selectedRows as $selectedIndex) {
                $selectedIndex = intval($selectedIndex);
                
                if (!isset($dataRows[$selectedIndex])) {
                    Log::warning('Row index not found', ['index' => $selectedIndex]);
                    continue;
                }
                
                $row = $dataRows[$selectedIndex];
                
                if ($this->isEmptyRow($row)) {
                    continue;
                }
                
                $processedRow = $this->processRow($row, $cleanHeaders, $selectedIndex);
                
                Log::info('Processing row for save', [
                    'row_index' => $selectedIndex,
                    'original_row' => $row,
                    'processed_row' => $processedRow
                ]);
                
                if (!$this->hasEssentialData($processedRow)) {
                    Log::warning('Row missing essential data', ['row' => $processedRow]);
                    continue;
                }
                
                try {
                    // Generate unique employee_id if missing
                    $employeeId = $processedRow['employee_id'] ?? null;
                    if (empty($employeeId)) {
                        $employeeId = 'EMP-' . date('Ymd') . '-' . str_pad($importedCount + 1, 4, '0', STR_PAD_LEFT);
                    }
                    
                    // Check for duplicate employee_id
                    if (Employee::where('employee_id', $employeeId)->exists()) {
                        $employeeId = $employeeId . '-' . time();
                    }
                    
                    $employeeData = [
                        'uid' => Str::uuid()->toString(),
                        'employee_id' => $employeeId,
                        'name' => $processedRow['name'] ?? 'Unknown',
                        'company_name' => $processedRow['company_name'] ?? null,
                        'work_location' => $processedRow['company_name'] ?? null,
                        'position' => $processedRow['position'] ?? 'Unknown',
                        'age' => $this->parseInteger($processedRow['age'] ?? null),
                        'date_of_birth' => $this->parseDate($processedRow['date_of_birth'] ?? null),
                        'resident_registration_number' => $processedRow['resident_registration_number'] ?? null,
                        'contact_number' => $processedRow['contact_number'] ?? null,
                        'date_of_joining' => $this->parseDate($processedRow['date_of_joining'] ?? null),
                        'employment_duration' => $processedRow['employment_duration'] ?? null,
                        'work_days' => $this->parseInteger($processedRow['work_days'] ?? null),
                        'base_salary' => $this->parseFloat($processedRow['base_salary'] ?? null),
                        'employment_status_key' => 'active',
                    ];
                    
                    Log::info('Creating employee', ['data' => $employeeData]);
                    
                    Employee::create($employeeData);
                    $importedCount++;
                } catch (\Exception $e) {
                    $error = 'Row ' . ($selectedIndex + 1) . ': ' . $e->getMessage();
                    $errors[] = $error;
                    Log::error('Failed to import employee row', [
                        'row_index' => $selectedIndex,
                        'error' => $e->getMessage(),
                        'row_data' => $processedRow,
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            Log::info('Import completed', [
                'imported_count' => $importedCount,
                'errors' => $errors
            ]);

            $response = [
                'success' => true,
                'message' => 'Import completed',
                'imported_count' => $importedCount
            ];
            
            if (!empty($errors)) {
                $response['warnings'] = $errors;
                $response['message'] .= ' with some warnings';
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Save preview error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Import error: ' . $e->getMessage()
            ]);
        }
    }

    private function parseInteger($value)
    {
        if (empty($value)) return null;
        return is_numeric($value) ? (int)$value : null;
    }

    private function parseFloat($value)
    {
        if (empty($value)) return null;
        return is_numeric($value) ? (float)$value : null;
    }

    private function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }
        
        try {
            // Handle Excel date serials
            if (is_numeric($dateValue)) {
                $unixDate = ($dateValue - 25569) * 86400;
                return Carbon::createFromTimestamp($unixDate)->format('Y-m-d');
            }
            
            // Try to parse as regular date
            return Carbon::parse($dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Failed to parse date: ' . $dateValue);
            return null;
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            $count = Employee::count();
            Employee::truncate();
            
            return response()->json([
                'success' => true,
                'message' => __('employee.management.delete_success'),
                'deleted_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('employee.management.import_error')
            ]);
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('employee_template.csv');
        
        if (file_exists($filePath)) {
            return response()->download($filePath, 'employee_template.csv', [
                'Content-Type' => 'text/csv',
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Template file not found.'
        ], 404);
    }
}
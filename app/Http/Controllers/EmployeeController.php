<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::all();
            return response()->json($employees);
        }
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
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
        $validated['uid'] = \Str::uuid()->toString(); // Generate a unique identifier
        $validated['date_of_joining'] = $validated['date_of_joining'] ? \Carbon\Carbon::parse($validated['date_of_joining']) : null;
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
        $validated['join_date'] = $validated['join_date'] ? \Carbon\Carbon::parse($validated['join_date']) : null;
        $validated['date_of_joining'] = $validated['date_of_joining'] ? \Carbon\Carbon::parse($validated['date_of_joining']) : null;
        $validated['date_of_birth'] = $validated['date_of_birth'] ? \Carbon\Carbon::parse($validated['date_of_birth']) : null;
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
        // You need to implement EmployeeImport or use a package like Laravel Excel
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
            
            // Read the raw data without using the EmployeeImport class
            $data = Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found in file'
                ]);
            }

            $allData = $data[0];
            
            // Skip first row (title), use second row as headers, data starts from row 3
            if (count($allData) < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'File must have at least 3 rows (title, headers, data)'
                ]);
            }

            // Row 1 (index 1) contains headers
            $headers = $allData[1];
            
            // Data starts from row 2 (index 2)
            $dataRows = array_slice($allData, 2);
            
            if (empty($dataRows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data rows found'
                ]);
            }

            // Convert data to associative arrays
            $processedRows = [];
            foreach ($dataRows as $index => $row) {
                $processedRow = [];
                for ($i = 0; $i < count($headers); $i++) {
                    $header = trim($headers[$i] ?? '');
                    // Use headers as-is if they match expected field names
                    $processedRow[$header] = $row[$i] ?? '';
                }
                $processedRow['row_index'] = $index; // Keep track of original index
                $processedRows[] = $processedRow;
            }
            
            // Add debugging
            \Log::info('Preview headers:', $headers);
            \Log::info('First processed row:', $processedRows[0] ?? []);
            
            // Preview first 10 data rows
            $previewRows = array_slice($processedRows, 0, 10);

            return response()->json([
                'success' => true,
                'data' => [
                    'headers' => $headers,
                    'rows' => $previewRows,
                    'total' => count($processedRows),
                    'start_row' => 3
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import error: ' . $e->getMessage()
            ]);
        }
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
            
            // Read the raw data without using the EmployeeImport class
            $data = Excel::toArray([], $file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found in file'
                ]);
            }

            $allData = $data[0];
            
            if (count($allData) < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'File must have at least 3 rows'
                ]);
            }

            // Row 1 (index 1) contains headers
            $headers = $allData[1];
            
            // Data starts from row 2 (index 2)
            $dataRows = array_slice($allData, 2);
            
            $importedCount = 0;

            foreach ($selectedRows as $rowIndex) {
                if (isset($dataRows[$rowIndex])) {
                    $row = $dataRows[$rowIndex];
                    
                    // Map the row data to field names using headers directly
                    $mappedData = [];
                    foreach ($row as $cellIndex => $cellValue) {
                        if (isset($headers[$cellIndex])) {
                            $header = trim($headers[$cellIndex]);
                            $mappedData[$header] = $cellValue;
                        }
                    }
                    
                    try {
                        Employee::create([
                            'uid' => \Str::uuid()->toString(),
                            'employee_id' => $mappedData['employee_id'] ?? null,
                            'name' => $mappedData['name'] ?? null,
                            'company_name' => $mappedData['company_name'] ?? null,
                            'position' => $mappedData['position'] ?? null,
                            'date_of_birth' => $mappedData['date_of_birth'] ?? null,
                            'resident_registration_number' => $mappedData['resident_registration_number'] ?? null,
                            'contact_number' => $mappedData['contact_number'] ?? null,
                            'date_of_joining' => $mappedData['date_of_joining'] ?? null,
                            'employment_duration' => $mappedData['employment_duration'] ?? null,
                            'work_days' => is_numeric($mappedData['work_days'] ?? null) ? (int)$mappedData['work_days'] : null,
                            'base_salary' => is_numeric($mappedData['base_salary'] ?? null) ? (float)$mappedData['base_salary'] : null,
                            'employment_status_key' => 'active',
                        ]);
                        $importedCount++;
                    } catch (\Exception $e) {
                        // Log the error for debugging
                        \Log::error('Failed to import employee row: ' . $e->getMessage(), ['row' => $mappedData]);
                        continue;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Import successful',
                'imported_count' => $importedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import error: ' . $e->getMessage()
            ]);
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
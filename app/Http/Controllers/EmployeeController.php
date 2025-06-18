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
        $validated['uid'] = \Str::uuid()->toString(); // Generate a unique identifier
        $validated['join_date'] = $validated['join_date'] ? \Carbon\Carbon::parse($validated['join_date']) : null; // Convert join_date to Carbon instance if provided
        $validated['date_of_joining'] = $validated['date_of_joining'] ? \Carbon\Carbon::parse($validated['date_of_joining']) : null;
        $validated['date_of_birth'] = $validated['date_of_birth'] ? \Carbon\Carbon::parse($validated['date_of_birth']) : null;
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
            'file' => 'required|file|mimes:xlsx,xls,csv',
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
            $data = Excel::toArray(new \App\Imports\EmployeeImport, $file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => __('employee.management.no_data_to_import')
                ]);
            }

            $headers = array_keys($data[0][0]);
            $rows = array_slice($data[0], 0, 10); // Preview first 10 rows

            return response()->json([
                'success' => true,
                'data' => [
                    'headers' => $headers,
                    'rows' => $rows,
                    'total' => count($data[0])
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('employee.management.import_error')
            ]);
        }
    }

    public function savePreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'selected_rows' => 'required|array',
        ]);

        try {
            $file = $request->file('file');
            $selectedRows = $request->input('selected_rows');
            $data = Excel::toArray(new \App\Imports\EmployeeImport, $file);
            
            if (empty($data) || empty($data[0])) {
                return response()->json([
                    'success' => false,
                    'message' => __('employee.management.no_data_to_import')
                ]);
            }

            $headers = array_keys($data[0][0]);
            $importedCount = 0;

            foreach ($selectedRows as $index) {
                if (isset($data[0][$index])) {
                    $row = $data[0][$index];
                    try {
                        Employee::create([
                            'uid' => \Str::uuid()->toString(),
                            'employee_id' => $row['employee_id'] ?? null,
                            'name' => $row['name'] ?? null,
                            'company_name' => $row['company_name'] ?? null,
                            'position' => $row['position'] ?? null,
                            'date_of_birth' => $row['date_of_birth'] ?? null,
                            'resident_registration_number' => $row['resident_registration_number'] ?? null,
                            'contact_number' => $row['contact_number'] ?? null,
                            'date_of_joining' => $row['date_of_joining'] ?? null,
                            'employment_duration' => $row['employment_duration'] ?? null,
                            'work_days' => $row['work_days'] ?? null,
                            'base_salary' => $row['base_salary'] ?? null,
                            'employment_status_key' => $row['employment_status_key'] ?? 'active',
                        ]);
                        $importedCount++;
                    } catch (\Exception $e) {
                        // Skip invalid rows
                        continue;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('employee.management.import_success'),
                'imported_count' => $importedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('employee.management.import_error')
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
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
            'work_location' => 'required|string',
            'position' => 'required|string',
            'name' => 'required|string',
            'age' => 'nullable|integer',
            'ssn' => 'nullable|string',
            'join_date' => 'nullable|date',
            'join_date_str' => 'nullable|string',
            'service_period' => 'nullable|string',
            'contact' => 'nullable|string',
            'base_salary' => 'nullable|numeric',
            'employment_status_key' => 'required|string',
            'employment_status_subtext' => 'nullable|string',
        ]);
        $validated['uid'] = \Str::uuid()->toString(); // Generate a unique identifier
        $validated['join_date'] = $validated['join_date'] ? \Carbon\Carbon::parse($validated['join_date']) : null; // Convert join_date to Carbon instance if provided
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
            'work_location' => 'required|string',
            'position' => 'required|string',
            'name' => 'required|string',
            'age' => 'nullable|integer',
            'ssn' => 'nullable|string',
            'join_date' => 'nullable|date',
            'join_date_str' => 'nullable|string',
            'service_period' => 'nullable|string',
            'contact' => 'nullable|string',
            'base_salary' => 'nullable|numeric',
            'employment_status_key' => 'required|string',
            'employment_status_subtext' => 'nullable|string',
        ]);
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
}
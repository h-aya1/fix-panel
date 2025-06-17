<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSummary;
use App\Imports\EmployeeSummaryImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class EmployeeSummaryController extends Controller
{
    /**
     * Display a listing of employee summaries.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $summaries = EmployeeSummary::orderBy('created_at', 'desc')->get();
            return response()->json($summaries);
        }

        $summaries = EmployeeSummary::orderBy('created_at', 'desc')->paginate(50);
        $totalRecords = EmployeeSummary::count();
        $latestImport = EmployeeSummary::latest('imported_at')->first();
        
        return view('employee-summaries.index', compact('summaries', 'totalRecords', 'latestImport'));
    }

    /**
     * Show the form for creating a new employee summary.
     */
    public function create()
    {
        return view('employee-summaries.create');
    }

    /**
     * Store a newly created employee summary.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'contact_number' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            // Add other validation rules as needed
        ]);

        EmployeeSummary::create($validated);

        return redirect()->route('employee-summaries.index')
                        ->with('success', __('employee_summary.created_successfully'));
    }

    /**
     * Display the specified employee summary.
     */
    public function show(EmployeeSummary $employeeSummary)
    {
        return view('employee-summaries.show', compact('employeeSummary'));
    }

    /**
     * Show the form for editing the specified employee summary.
     */
    public function edit(EmployeeSummary $employeeSummary)
    {
        return view('employee-summaries.edit', compact('employeeSummary'));
    }

    /**
     * Update the specified employee summary.
     */
    public function update(Request $request, EmployeeSummary $employeeSummary)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0',
            'contact_number' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            // Add other validation rules as needed
        ]);

        $employeeSummary->update($validated);

        return redirect()->route('employee-summaries.index')
                        ->with('success', __('employee_summary.updated_successfully'));
    }

    /**
     * Remove the specified employee summary.
     */
    public function destroy(EmployeeSummary $employeeSummary)
    {
        $employeeSummary->delete();

        return redirect()->route('employee-summaries.index')
                        ->with('success', __('employee_summary.deleted_successfully'));
    }

    /**
     * Show the import form.
     */
    public function importForm()
    {
        return view('employee-summaries.import');
    }

    /**
     * Handle the import process.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            DB::beginTransaction();

            $import = new EmployeeSummaryImport();
            Excel::import($import, $request->file('file'));

            DB::commit();

            $importedCount = EmployeeSummary::where('import_batch', $import->getImportBatch())->count();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('employee_summary.import_successful', ['count' => $importedCount]),
                    'imported_count' => $importedCount
                ]);
            }

            return redirect()->route('employee-summaries.index')
                            ->with('success', __('employee_summary.import_successful', ['count' => $importedCount]));

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('employee_summary.import_failed'),
                    'error' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                            ->with('error', __('employee_summary.import_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Delete all employee summaries.
     */
    public function deleteAll()
    {
        try {
            $count = EmployeeSummary::count();
            EmployeeSummary::truncate();

            return response()->json([
                'success' => true,
                'message' => __('employee_summary.deleted_all_successful', ['count' => $count])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('employee_summary.delete_all_failed'),
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get import statistics.
     */
    public function getImportStats()
    {
        $stats = [
            'total_records' => EmployeeSummary::count(),
            'latest_import' => EmployeeSummary::latest('imported_at')->first()?->imported_at,
            'import_batches' => EmployeeSummary::distinct('import_batch')->count('import_batch'),
        ];

        return response()->json($stats);
    }
}

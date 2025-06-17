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
     * Preview the data from uploaded file without saving.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $data = Excel::toArray(new EmployeeSummaryImport(), $request->file('file'));
            
            // Get the first sheet data
            $sheetData = $data[0] ?? [];
            
            // Process and format the data for preview
            $previewData = [];
            $headers = [];
            
            foreach ($sheetData as $index => $row) {
                if ($index === 0) {
                    // First row contains headers
                    $headers = array_values($row);
                    continue;
                }
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                $rowData = [
                    'row_index' => $index,
                    'employee_id' => $row[0] ?? '',
                    'name' => $row[1] ?? '',
                    'company_name' => $row[2] ?? '',
                    'position' => $row[3] ?? '',
                    'age' => is_numeric($row[4] ?? '') ? (int)$row[4] : null,
                    'work_days' => is_numeric($row[5] ?? '') ? (int)$row[5] : null,
                    'base_salary' => is_numeric($row[6] ?? '') ? (float)$row[6] : null,
                    'total_earnings' => is_numeric($row[7] ?? '') ? (float)$row[7] : null,
                    'total_deductions' => is_numeric($row[8] ?? '') ? (float)$row[8] : null,
                    'net_payment' => is_numeric($row[9] ?? '') ? (float)$row[9] : null,
                    'contact_number' => $row[10] ?? '',
                    'date_of_joining' => $this->parseDate($row[11] ?? ''),
                ];
                
                $previewData[] = $rowData;
            }

            return response()->json([
                'success' => true,
                'data' => $previewData,
                'headers' => $headers,
                'total_rows' => count($previewData)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('employee_summary.preview_failed'),
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Save selected data from preview.
     */
    public function savePreview(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.employee_id' => 'nullable|string|max:255',
            'data.*.name' => 'required|string|max:255',
            'data.*.company_name' => 'nullable|string|max:255',
            'data.*.position' => 'nullable|string|max:255',
            'data.*.age' => 'nullable|integer|min:0',
            'data.*.work_days' => 'nullable|integer|min:0',
            'data.*.base_salary' => 'nullable|numeric|min:0',
            'data.*.total_earnings' => 'nullable|numeric|min:0',
            'data.*.total_deductions' => 'nullable|numeric|min:0',
            'data.*.net_payment' => 'nullable|numeric',
            'data.*.contact_number' => 'nullable|string|max:255',
            'data.*.date_of_joining' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $importBatch = 'preview_' . uniqid();
            $importedCount = 0;

            foreach ($request->data as $rowData) {
                EmployeeSummary::create([
                    'employee_id' => $rowData['employee_id'],
                    'name' => $rowData['name'],
                    'company_name' => $rowData['company_name'],
                    'position' => $rowData['position'],
                    'age' => $rowData['age'],
                    'work_days' => $rowData['work_days'],
                    'base_salary' => $rowData['base_salary'],
                    'total_earnings' => $rowData['total_earnings'],
                    'total_deductions' => $rowData['total_deductions'],
                    'net_payment' => $rowData['net_payment'],
                    'contact_number' => $rowData['contact_number'],
                    'date_of_joining' => $rowData['date_of_joining'],
                    'import_batch' => $importBatch,
                    'imported_at' => now(),
                ]);
                $importedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('employee_summary.import_successful', ['count' => $importedCount]),
                'imported_count' => $importedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('employee_summary.import_failed'),
                'error' => $e->getMessage()
            ], 422);
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

    /**
     * Helper method to parse date strings.
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Handle Excel serial date numbers
            if (is_numeric($dateString)) {
                $unixDate = ($dateString - 25569) * 86400;
                return date('Y-m-d', $unixDate);
            }
            
            // Handle various date formats
            $date = \DateTime::createFromFormat('Y-m-d', $dateString);
            if ($date) {
                return $date->format('Y-m-d');
            }
            
            $date = \DateTime::createFromFormat('m/d/Y', $dateString);
            if ($date) {
                return $date->format('Y-m-d');
            }
            
            $date = \DateTime::createFromFormat('d/m/Y', $dateString);
            if ($date) {
                return $date->format('Y-m-d');
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}

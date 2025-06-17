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
            $query = EmployeeSummary::query();
            
            // Apply company filter if provided
            if ($request->has('company') && $request->company !== '') {
                $query->where('company_name', $request->company);
            }
            
            $summaries = $query->orderBy('created_at', 'desc')->get();
            return response()->json($summaries);
        }

        $query = EmployeeSummary::query();
        
        // Apply company filter if provided
        if ($request->has('company') && $request->company !== '') {
            $query->where('company_name', $request->company);
        }

        $summaries = $query->orderBy('created_at', 'desc')->paginate(50);
        $totalRecords = EmployeeSummary::count();
        $latestImport = EmployeeSummary::latest('imported_at')->first();
        
        // Get distinct companies for filter dropdown
        $companies = EmployeeSummary::whereNotNull('company_name')
                                   ->where('company_name', '!=', '')
                                   ->distinct()
                                   ->pluck('company_name')
                                   ->sort()
                                   ->values();
        
        return view('employee-summaries.index', compact('summaries', 'totalRecords', 'latestImport', 'companies'));
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
                    'no' => is_numeric($row[0] ?? '') ? (int)$row[0] : null,
                    'employee_id' => $row[1] ?? '',
                    'company_name' => $row[2] ?? '',
                    'position' => $row[3] ?? '',
                    'name' => $row[4] ?? '',
                    'age' => is_numeric($row[5] ?? '') ? (int)$row[5] : null,
                    'resident_registration_number' => $row[6] ?? '',
                    'date_of_joining' => $this->parseDate($row[7] ?? ''),
                    'contact_number' => $row[8] ?? '',
                    'work_days' => is_numeric($row[9] ?? '') ? (int)$row[9] : null,
                    'base_salary' => is_numeric($row[10] ?? '') ? (float)$row[10] : null,
                    'qualification_allowance' => is_numeric($row[11] ?? '') ? (float)$row[11] : null,
                    'position_allowance' => is_numeric($row[12] ?? '') ? (float)$row[12] : null,
                    'duty_allowance' => is_numeric($row[13] ?? '') ? (float)$row[13] : null,
                    'overtime_allowance' => is_numeric($row[14] ?? '') ? (float)$row[14] : null,
                    'holiday_work_allowance' => is_numeric($row[15] ?? '') ? (float)$row[15] : null,
                    'night_shift_allowance' => is_numeric($row[16] ?? '') ? (float)$row[16] : null,
                    'bonus' => is_numeric($row[17] ?? '') ? (float)$row[17] : null,
                    'adjustment_allowance' => is_numeric($row[18] ?? '') ? (float)$row[18] : null,
                    'transportation_allowance' => is_numeric($row[19] ?? '') ? (float)$row[19] : null,
                    'meal_allowance' => is_numeric($row[20] ?? '') ? (float)$row[20] : null,
                    'labor_day_allowance' => is_numeric($row[21] ?? '') ? (float)$row[21] : null,
                    'paid_leave_allowance' => is_numeric($row[22] ?? '') ? (float)$row[22] : null,
                    'welfare_allowance' => is_numeric($row[23] ?? '') ? (float)$row[23] : null,
                    'other_allowances' => is_numeric($row[24] ?? '') ? (float)$row[24] : null,
                    'total_earnings' => is_numeric($row[25] ?? '') ? (float)$row[25] : null,
                    'health_insurance' => is_numeric($row[26] ?? '') ? (float)$row[26] : null,
                    'long_term_care_insurance' => is_numeric($row[27] ?? '') ? (float)$row[27] : null,
                    'employment_insurance' => is_numeric($row[28] ?? '') ? (float)$row[28] : null,
                    'national_pension' => is_numeric($row[29] ?? '') ? (float)$row[29] : null,
                    'income_tax' => is_numeric($row[30] ?? '') ? (float)$row[30] : null,
                    'local_income_tax' => is_numeric($row[31] ?? '') ? (float)$row[31] : null,
                    'other_deductions' => is_numeric($row[32] ?? '') ? (float)$row[32] : null,
                    'total_deductions' => is_numeric($row[33] ?? '') ? (float)$row[33] : null,
                    'net_payment' => is_numeric($row[34] ?? '') ? (float)$row[34] : null,
                    'remarks' => $row[35] ?? '',
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
            'data.*.no' => 'nullable|integer',
            'data.*.employee_id' => 'nullable|string|max:255',
            'data.*.name' => 'required|string|max:255',
            'data.*.company_name' => 'nullable|string|max:255',
            'data.*.position' => 'nullable|string|max:255',
            'data.*.age' => 'nullable|integer|min:0',
            'data.*.resident_registration_number' => 'nullable|string|max:255',
            'data.*.date_of_joining' => 'nullable|date',
            'data.*.contact_number' => 'nullable|string|max:255',
            'data.*.work_days' => 'nullable|integer|min:0',
            'data.*.base_salary' => 'nullable|numeric|min:0',
            'data.*.qualification_allowance' => 'nullable|numeric|min:0',
            'data.*.position_allowance' => 'nullable|numeric|min:0',
            'data.*.duty_allowance' => 'nullable|numeric|min:0',
            'data.*.overtime_allowance' => 'nullable|numeric|min:0',
            'data.*.holiday_work_allowance' => 'nullable|numeric|min:0',
            'data.*.night_shift_allowance' => 'nullable|numeric|min:0',
            'data.*.bonus' => 'nullable|numeric|min:0',
            'data.*.adjustment_allowance' => 'nullable|numeric',
            'data.*.transportation_allowance' => 'nullable|numeric|min:0',
            'data.*.meal_allowance' => 'nullable|numeric|min:0',
            'data.*.labor_day_allowance' => 'nullable|numeric|min:0',
            'data.*.paid_leave_allowance' => 'nullable|numeric|min:0',
            'data.*.welfare_allowance' => 'nullable|numeric|min:0',
            'data.*.other_allowances' => 'nullable|numeric|min:0',
            'data.*.total_earnings' => 'nullable|numeric|min:0',
            'data.*.health_insurance' => 'nullable|numeric|min:0',
            'data.*.long_term_care_insurance' => 'nullable|numeric|min:0',
            'data.*.employment_insurance' => 'nullable|numeric|min:0',
            'data.*.national_pension' => 'nullable|numeric|min:0',
            'data.*.income_tax' => 'nullable|numeric|min:0',
            'data.*.local_income_tax' => 'nullable|numeric|min:0',
            'data.*.other_deductions' => 'nullable|numeric|min:0',
            'data.*.total_deductions' => 'nullable|numeric|min:0',
            'data.*.net_payment' => 'nullable|numeric',
            'data.*.remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $importBatch = 'preview_' . uniqid();
            $importedCount = 0;

            foreach ($request->data as $rowData) {
                EmployeeSummary::create([
                    'no' => $rowData['no'],
                    'employee_id' => $rowData['employee_id'],
                    'name' => $rowData['name'],
                    'company_name' => $rowData['company_name'],
                    'position' => $rowData['position'],
                    'age' => $rowData['age'],
                    'resident_registration_number' => $rowData['resident_registration_number'],
                    'date_of_joining' => $rowData['date_of_joining'],
                    'contact_number' => $rowData['contact_number'],
                    'work_days' => $rowData['work_days'],
                    'base_salary' => $rowData['base_salary'],
                    'qualification_allowance' => $rowData['qualification_allowance'],
                    'position_allowance' => $rowData['position_allowance'],
                    'duty_allowance' => $rowData['duty_allowance'],
                    'overtime_allowance' => $rowData['overtime_allowance'],
                    'holiday_work_allowance' => $rowData['holiday_work_allowance'],
                    'night_shift_allowance' => $rowData['night_shift_allowance'],
                    'bonus' => $rowData['bonus'],
                    'adjustment_allowance' => $rowData['adjustment_allowance'],
                    'transportation_allowance' => $rowData['transportation_allowance'],
                    'meal_allowance' => $rowData['meal_allowance'],
                    'labor_day_allowance' => $rowData['labor_day_allowance'],
                    'paid_leave_allowance' => $rowData['paid_leave_allowance'],
                    'welfare_allowance' => $rowData['welfare_allowance'],
                    'other_allowances' => $rowData['other_allowances'],
                    'total_earnings' => $rowData['total_earnings'],
                    'health_insurance' => $rowData['health_insurance'],
                    'long_term_care_insurance' => $rowData['long_term_care_insurance'],
                    'employment_insurance' => $rowData['employment_insurance'],
                    'national_pension' => $rowData['national_pension'],
                    'income_tax' => $rowData['income_tax'],
                    'local_income_tax' => $rowData['local_income_tax'],
                    'other_deductions' => $rowData['other_deductions'],
                    'total_deductions' => $rowData['total_deductions'],
                    'net_payment' => $rowData['net_payment'],
                    'remarks' => $rowData['remarks'],
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

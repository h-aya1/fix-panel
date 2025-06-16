<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $payrolls = Payroll::all();
            return response()->json($payrolls);
        }
        
        // Get current payroll data from database
        $payrolls = Payroll::orderBy('created_at', 'desc')->get();
        
        // Format payroll data for the view
        $payrollEntriesData = [];
        foreach ($payrolls as $payroll) {
            $allowanceItems = is_string($payroll->allowance_items) 
                ? json_decode($payroll->allowance_items, true) ?: []
                : ($payroll->allowance_items ?: []);
                
            $deductionItems = is_string($payroll->deduction_items)
                ? json_decode($payroll->deduction_items, true) ?: []
                : ($payroll->deduction_items ?: []);

            $payrollEntriesData[] = [
                'uid' => $payroll->uid ?: 'payroll_' . $payroll->id,
                'id' => $payroll->employee_id,
                'name' => $payroll->name,
                'department' => $payroll->department,
                'position' => $payroll->position,
                'phone_number' => $payroll->phone_number,
                'work_days' => $payroll->work_days,
                'base_salary_str' => number_format($payroll->base_salary ?: 0),
                'allowances_str' => number_format($payroll->allowances ?: 0),
                'gross_pay_str' => number_format($payroll->gross_pay ?: 0),
                'deductions_str' => number_format($payroll->deductions ?: 0),
                'net_pay_str' => number_format($payroll->net_pay ?: 0),
                'remarks' => $payroll->remarks ?: '',
                'sms_sent_status' => $payroll->sms_sent_status ?: 'pending',
                'is_checked' => $payroll->is_checked ?? true,
                'numeric_base_salary' => $payroll->base_salary ?: 0,
                'numeric_total_allowances' => $payroll->allowances ?: 0,
                'numeric_total_deductions' => $payroll->deductions ?: 0,
                'numeric_net_pay' => $payroll->net_pay ?: 0,
                'allowance_items' => $allowanceItems,
                'deduction_items' => $deductionItems,
                'sms_details' => null // Will be populated when needed
            ];
        }
        
        // If no payroll data exists, provide empty array
        if (empty($payrollEntriesData)) {
            $payrollEntriesData = [];
        }
        
        // Current month and statistics
        $currentPayrollMonth = now()->format('Y년 n월');
        $totalPayrollEmployees = $payrolls->count();
        $smsSentCount = $payrolls->where('sms_sent_status', 'sent')->count();
        
        // Default SMS details structure (for first employee if exists)
        $imChaeJeongSmsDetails = [
            'company_name' => __('payroll.offcanvas_sms.sms_company_name'),
            'intro_line1' => __('payroll.offcanvas_sms.sms_intro_line1'),
            'intro_line2' => __('payroll.offcanvas_sms.sms_intro_line2'),
            'link_url' => 'www.example.com/payslip/sample',
            'link_text' => __('payroll.offcanvas_sms.sms_link_text'),
            'payment_date' => now()->format('Y년 m월 d일'),
            'statement_title' => __('payroll.offcanvas_sms.sms_statement_title_prefix').' '. __('payroll.offcanvas_sms.sms_statement_title_suffix'),
            'earnings' => [],
            'total_gross_pay_label' => __('payroll.offcanvas_sms.sms_total_gross_pay_label'),
            'total_gross_pay_value' => 0,
            'deductions' => [],
            'total_deductions_label' => __('payroll.offcanvas_sms.sms_total_deductions_label'),
            'total_deductions_value' => 0,
        ];
        
        // Attendance/leave example data
        $attendanceLeaveDataExample = [
            [
                'type' => __('offcanvas.attendance.table.header.type'),
                'date' => now()->subDays(5)->format('Y.m.d') . ' - ' . now()->subDays(3)->format('Y.m.d'),
                'period' => '3일',
                'paid' => 'X',
                'memo' => '가족 행사'
            ]
        ];
        
        return view('payrolls.index', compact(
            'payrolls', 
            'payrollEntriesData',
            'currentPayrollMonth', 
            'totalPayrollEmployees', 
            'smsSentCount',
            'imChaeJeongSmsDetails',
            'attendanceLeaveDataExample'
        ));
    }

    /**
     * Get employees for payroll creation
     */
    public function getEmployees(Request $request)
    {
        try {
            $employees = Employee::select('id', 'employee_id', 'name', 'work_location as department', 'position', 'contact as phone_number', 'base_salary')
                               ->orderBy('name')
                               ->get();

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific employee data
     */
    public function getEmployee(Request $request, $employeeId)
    {
        try {
            $employee = Employee::where('id', $employeeId)->first();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'employee' => [
                    'employee_id' => $employee->employee_id,
                    'name' => $employee->name,
                    'department' => $employee->work_location,
                    'position' => $employee->position,
                    'phone_number' => $employee->contact,
                    'base_salary' => $employee->base_salary
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|string',
                'department' => 'required|string',
                'position' => 'required|string',
                'name' => 'required|string',
                'phone_number' => 'nullable|string',
                'work_days' => 'required|integer|min:0',
                'base_salary' => 'required|numeric|min:0',
                'allowances' => 'nullable|numeric|min:0',
                'deductions' => 'nullable|numeric|min:0',
                'remarks' => 'nullable|string',
                'payroll_month' => 'nullable|string',
                'payroll_year' => 'nullable|integer',
                'allowance_items' => 'nullable|array',
                'allowance_items.*.type' => 'required_with:allowance_items|string',
                'allowance_items.*.amount' => 'required_with:allowance_items|numeric|min:0',
                'deduction_items' => 'nullable|array',
                'deduction_items.*.type' => 'required_with:deduction_items|string',
                'deduction_items.*.amount' => 'required_with:deduction_items|numeric|min:0',
            ]);

            $validated['uid'] = Str::uuid()->toString();
            
            // Process allowance items and calculate total
            $totalAllowances = 0;
            if (!empty($validated['allowance_items'])) {
                foreach ($validated['allowance_items'] as &$item) {
                    $item['label_translation'] = $this->getAllowanceLabel($item['type']);
                    $totalAllowances += $item['amount'];
                }
                unset($item); // Break reference
            } else {
                $validated['allowance_items'] = [];
            }
            
            // Process deduction items and calculate total
            $totalDeductions = 0;
            if (!empty($validated['deduction_items'])) {
                foreach ($validated['deduction_items'] as &$item) {
                    $item['label_translation'] = $this->getDeductionLabel($item['type']);
                    $totalDeductions += $item['amount'];
                }
                unset($item); // Break reference
            } else {
                $validated['deduction_items'] = [];
            }
            
            // Use calculated totals or provided values
            $validated['allowances'] = $totalAllowances > 0 ? $totalAllowances : ($validated['allowances'] ?? 0);
            $validated['deductions'] = $totalDeductions > 0 ? $totalDeductions : ($validated['deductions'] ?? 0);
            
            // Calculate gross and net pay
            $validated['gross_pay'] = $validated['base_salary'] + $validated['allowances'];
            $validated['net_pay'] = $validated['gross_pay'] - $validated['deductions'];
            
            // Set numeric values
            $validated['numeric_base_salary'] = $validated['base_salary'];
            $validated['numeric_total_allowances'] = $validated['allowances'];
            $validated['numeric_total_deductions'] = $validated['deductions'];
            $validated['numeric_net_pay'] = $validated['net_pay'];
            
            // Format string values
            $validated['base_salary_str'] = number_format($validated['base_salary'], 0);
            $validated['allowances_str'] = number_format($validated['allowances'], 0);
            $validated['gross_pay_str'] = number_format($validated['gross_pay'], 0);
            $validated['deductions_str'] = number_format($validated['deductions'], 0);
            $validated['net_pay_str'] = number_format($validated['net_pay'], 0);
            
            // Set default values
            $validated['sms_sent_status'] = 'pending';
            $validated['is_checked'] = true;

            $payroll = Payroll::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('payroll.created_successfully'),
                    'data' => $payroll
                ]);
            }

            Session::flash('success', __('payroll.created_successfully'));
            return redirect()->route('payrolls.index');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('payroll.creation_failed'),
                    'error' => $e->getMessage()
                ], 422);
            }

            Session::flash('error', __('payroll.creation_failed'));
            return redirect()->back()->withInput();
        }
    }

    /**
     * Get allowance label translation
     */
    private function getAllowanceLabel($type)
    {
        $labels = [
            'seniority' => __('payroll.offcanvas_details.allowances.seniority'),
            'position' => __('payroll.offcanvas_details.allowances.position'),
            'job' => __('payroll.offcanvas_details.allowances.job'),
            'overtime' => __('payroll.offcanvas_details.allowances.overtime'),
            'holiday_special_work' => __('payroll.offcanvas_details.allowances.holiday_special_work'),
            'night_shift' => __('payroll.offcanvas_details.allowances.night_shift'),
            'bonus' => __('payroll.offcanvas_details.allowances.bonus'),
            'adjustment' => __('payroll.offcanvas_details.allowances.adjustment'),
            'transportation' => __('payroll.offcanvas_details.allowances.transportation'),
            'meal' => __('payroll.offcanvas_details.allowances.meal'),
            'labor_day' => __('payroll.offcanvas_details.allowances.labor_day'),
            'annual_leave' => __('payroll.offcanvas_details.allowances.annual_leave'),
            'welfare' => __('payroll.offcanvas_details.allowances.welfare'),
            'other' => __('payroll.offcanvas_details.allowances.other'),
        ];
        
        return $labels[$type] ?? $type;
    }

    /**
     * Get deduction label translation
     */
    private function getDeductionLabel($type)
    {
        $labels = [
            'health_insurance' => __('payroll.offcanvas_details.deductions.health_insurance'),
            'long_term_care_insurance' => __('payroll.offcanvas_details.deductions.long_term_care_insurance'),
            'employment_insurance' => __('payroll.offcanvas_details.deductions.employment_insurance'),
            'national_pension' => __('payroll.offcanvas_details.deductions.national_pension'),
            'income_tax' => __('payroll.offcanvas_details.deductions.income_tax'),
            'local_income_tax' => __('payroll.offcanvas_details.deductions.local_income_tax'),
            'other' => __('payroll.offcanvas_details.deductions.other'),
        ];
        
        return $labels[$type] ?? $type;
    }

    public function update(Request $request, $id)
    {
        try {
            $payroll = Payroll::findOrFail($id);
            
            $validated = $request->validate([
                'employee_id' => 'required|string',
                'department' => 'required|string',
                'position' => 'required|string',
                'name' => 'required|string',
                'phone_number' => 'nullable|string',
                'work_days' => 'required|integer|min:0',
                'base_salary' => 'required|numeric|min:0',
                'allowances' => 'nullable|numeric|min:0',
                'deductions' => 'nullable|numeric|min:0',
                'remarks' => 'nullable|string',
                'payroll_month' => 'nullable|string',
                'payroll_year' => 'nullable|integer',
                'allowance_items' => 'nullable|array',
                'deduction_items' => 'nullable|array',
                'sms_sent_status' => 'nullable|in:sent,pending,failed',
            ]);

            $validated['allowances'] = $validated['allowances'] ?? 0;
            $validated['deductions'] = $validated['deductions'] ?? 0;
            
            // Calculate gross and net pay
            $validated['gross_pay'] = $validated['base_salary'] + $validated['allowances'];
            $validated['net_pay'] = $validated['gross_pay'] - $validated['deductions'];
            
            // Set numeric values
            $validated['numeric_base_salary'] = $validated['base_salary'];
            $validated['numeric_total_allowances'] = $validated['allowances'];
            $validated['numeric_total_deductions'] = $validated['deductions'];
            $validated['numeric_net_pay'] = $validated['net_pay'];
            
            // Format string values
            $validated['base_salary_str'] = number_format($validated['base_salary'], 0);
            $validated['allowances_str'] = number_format($validated['allowances'], 0);
            $validated['gross_pay_str'] = number_format($validated['gross_pay'], 0);
            $validated['deductions_str'] = number_format($validated['deductions'], 0);
            $validated['net_pay_str'] = number_format($validated['net_pay'], 0);

            $payroll->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('payroll.updated_successfully'),
                    'data' => $payroll
                ]);
            }

            Session::flash('success', __('payroll.updated_successfully'));
            return redirect()->route('payrolls.index');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('payroll.update_failed'),
                    'error' => $e->getMessage()
                ], 422);
            }

            Session::flash('error', __('payroll.update_failed'));
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $payroll = Payroll::findOrFail($id);
            $payroll->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('payroll.deleted_successfully')
                ]);
            }

            Session::flash('success', __('payroll.deleted_successfully'));
            return redirect()->route('payrolls.index');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('payroll.deletion_failed'),
                    'error' => $e->getMessage()
                ], 422);
            }

            Session::flash('error', __('payroll.deletion_failed'));
            return redirect()->back();
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:payrolls,id'
            ])['ids'];

            Payroll::whereIn('id', $ids)->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('payroll.bulk_deleted_successfully', ['count' => count($ids)])
                ]);
            }

            Session::flash('success', __('payroll.bulk_deleted_successfully', ['count' => count($ids)]));
            return redirect()->route('payrolls.index');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('payroll.bulk_deletion_failed'),
                    'error' => $e->getMessage()
                ], 422);
            }

            Session::flash('error', __('payroll.bulk_deletion_failed'));
            return redirect()->back();
        }
    }

    public function updateSmsStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:payrolls,id',
                'status' => 'required|in:sent,pending,failed'
            ]);

            Payroll::whereIn('id', $validated['ids'])->update([
                'sms_sent_status' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => __('payroll.sms_status_updated', ['count' => count($validated['ids'])])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('payroll.sms_status_update_failed'),
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            Excel::import(new \App\Imports\PayrollImport, $request->file('file'));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('payroll.imported_successfully')
                ]);
            }

            Session::flash('success', __('payroll.imported_successfully'));
            return redirect()->route('payrolls.index');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('payroll.import_failed'),
                    'error' => $e->getMessage()
                ], 422);
            }

            Session::flash('error', __('payroll.import_failed'));
            return redirect()->back();
        }
    }

    public function print()
    {
        $year = 2025; // from image data
        $month = 3; // from image data (March)
        $data = [
            'year' => $year,
            'month_numeric' => str_pad($month, 2, '0', STR_PAD_LEFT),
            // Use Carbon or similar for month name localization if needed
            'month_name' => \Carbon\Carbon::createFromDate($year, $month, 1)->isoFormat('MMMM'), // Example: March
            'employee_id' => '587', // from image data
            'employee_name' => '임채종', // from image data
            'payment_date' => '2025.05.20', // from image data
            'net_pay_amount' => '3020202', // from image data
            'earnings' => [
                'regular' => [
                    ['label_key' => 'payroll.payslip.earning_items.base_salary', 'value' => '2000000'], // Example value
                    ['label_key' => 'payroll.payslip.earning_items.position_allowance', 'value' => '200000'],
                    ['label_key' => 'payroll.payslip.earning_items.meal_allowance', 'value' => '100000'],
                    ['label_key' => 'payroll.payslip.earning_items.night_shift_allowance', 'value' => null],
                ],
                'irregular' => [
                    ['label_key' => null, 'value' => '50000'], // Example Incentive
                    ['label_key' => null, 'value' => null],
                    // ... fill other 7 irregular slots or leave as null for empty rows
                    ['label_key' => null, 'value' => null], ['label_key' => null, 'value' => null],
                    ['label_key' => null, 'value' => null], ['label_key' => null, 'value' => null],
                    ['label_key' => null, 'value' => null], ['label_key' => null, 'value' => null],
                ]
            ],
            'deductions' => [
                ['label_key' => 'payroll.payslip.deduction_items.health_insurance', 'value' => '60000'], // Example value
                ['label_key' => 'payroll.payslip.deduction_items.long_term_care_insurance', 'value' => '7500'],
                ['label_key' => 'payroll.payslip.deduction_items.employment_insurance', 'value' => '16000'],
                ['label_key' => 'payroll.payslip.deduction_items.national_pension', 'value' => '90000'],
                ['label_key' => 'payroll.payslip.deduction_items.income_tax', 'value' => '40000'],
                ['label_key' => 'payroll.payslip.deduction_items.local_income_tax', 'value' => '4000'],
                ['label_key' => 'payroll.payslip.deduction_items.other_deductions', 'value' => '5600'], // Example value
                // ... fill other 5 deduction slots or leave as null for empty rows
                ['label_key' => null, 'value' => null], ['label_key' => null, 'value' => null],
                ['label_key' => null, 'value' => null], ['label_key' => null, 'value' => null],
                ['label_key' => null, 'value' => null], ['label_key' => null, 'value' => null],
                ['label_key' => null, 'value' => null],
            ],
            'total_earnings' => '223102',
            'total_deductions' => '223102',
        ];

        return view('payrolls.print', ['payslipData' => $data]);
    }
}

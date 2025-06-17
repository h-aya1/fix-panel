@extends('layouts.app')

@section('title', __('employee_summary.detail_title'))

@section('page-style')
<style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 0.9rem; background-color: #f4f5f7; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e0e0e0; }
    .header-main-title { font-size: 1.75rem; font-weight: 600; color: #333; }
    .info-card { background: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }
    .info-card h5 { color: #0d6efd; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem; margin-bottom: 1rem; }
    .info-row { display: flex; margin-bottom: 0.75rem; }
    .info-label { font-weight: 600; min-width: 200px; color: #555; }
    .info-value { color: #333; }
    .currency { text-align: right; font-weight: 600; color: #198754; }
    .duration-badge { background-color: #e7f3ff; color: #0066cc; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem; font-weight: 500; }
    .back-btn { margin-bottom: 1rem; }
    .no-data { text-align: center; color: #6c757d; font-style: italic; padding: 2rem; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Back Button -->
    <div class="back-btn">
        <a href="{{ route('employee-summaries.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>{{ __('app.back') }}
        </a>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="header-main-title mb-1">{{ __('employee_summary.detail_title') }}</h1>
            <div class="text-muted">{{ $employeeSummary->name }} - {{ $employeeSummary->employee_id }}</div>
        </div>
        <div>
            <span class="duration-badge">
                <i class="bx bx-time me-1"></i>{{ $employeeSummary->employment_duration ?: __('employee_summary.duration_na') }}
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6 mb-4">
            <div class="info-card">
                <h5><i class="bx bx-user me-2"></i>{{ __('employee_summary.basic_info') }}</h5>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.no') }}:</div>
                    <div class="info-value">{{ $employeeSummary->no ?: '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.employee_id') }}:</div>
                    <div class="info-value">{{ $employeeSummary->employee_id ?: '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.name') }}:</div>
                    <div class="info-value">{{ $employeeSummary->name }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.company') }}:</div>
                    <div class="info-value">{{ $employeeSummary->company_name ?: '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.position') }}:</div>
                    <div class="info-value">{{ $employeeSummary->position ?: '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.age') }}:</div>
                    <div class="info-value">{{ $employeeSummary->age ?: '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.resident_registration_number') }}:</div>
                    <div class="info-value">{{ $employeeSummary->resident_registration_number ?: '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.contact') }}:</div>
                    <div class="info-value">{{ $employeeSummary->contact_number ?: '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.join_date') }}:</div>
                    <div class="info-value">{{ $employeeSummary->date_of_joining ? $employeeSummary->date_of_joining->format('Y-m-d') : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.employment_duration') }}:</div>
                    <div class="info-value">
                        <span class="duration-badge">{{ $employeeSummary->employment_duration ?: __('employee_summary.duration_na') }}</span>
                    </div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.remarks') }}:</div>
                    <div class="info-value">{{ $employeeSummary->remarks ?: '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Salary Information -->
        <div class="col-md-6 mb-4">
            <div class="info-card">
                <h5><i class="bx bx-money me-2"></i>{{ __('employee_summary.salary_info') }}</h5>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.work_days') }}:</div>
                    <div class="info-value">{{ $employeeSummary->work_days ?: '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.base_salary') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->base_salary ? number_format($employeeSummary->base_salary) : '-' }}</div>
                </div>
                
                <h6 class="mt-3 mb-2 text-success">{{ __('employee_summary.allowances') }}</h6>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.qualification_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->qualification_allowance ? number_format($employeeSummary->qualification_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.position_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->position_allowance ? number_format($employeeSummary->position_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.duty_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->duty_allowance ? number_format($employeeSummary->duty_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.overtime_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->overtime_allowance ? number_format($employeeSummary->overtime_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.holiday_work_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->holiday_work_allowance ? number_format($employeeSummary->holiday_work_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.night_shift_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->night_shift_allowance ? number_format($employeeSummary->night_shift_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.bonus') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->bonus ? number_format($employeeSummary->bonus) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.adjustment_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->adjustment_allowance ? number_format($employeeSummary->adjustment_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.transportation_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->transportation_allowance ? number_format($employeeSummary->transportation_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.meal_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->meal_allowance ? number_format($employeeSummary->meal_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.labor_day_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->labor_day_allowance ? number_format($employeeSummary->labor_day_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.paid_leave_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->paid_leave_allowance ? number_format($employeeSummary->paid_leave_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.welfare_allowance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->welfare_allowance ? number_format($employeeSummary->welfare_allowance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.other_allowances') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->other_allowances ? number_format($employeeSummary->other_allowances) : '-' }}</div>
                </div>
                
                <h6 class="mt-3 mb-2 text-primary">{{ __('employee_summary.totals') }}</h6>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.total_earnings') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->total_earnings ? number_format($employeeSummary->total_earnings) : '-' }}</div>
                </div>
                
                <h6 class="mt-3 mb-2 text-danger">{{ __('employee_summary.deductions') }}</h6>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.health_insurance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->health_insurance ? number_format($employeeSummary->health_insurance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.long_term_care_insurance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->long_term_care_insurance ? number_format($employeeSummary->long_term_care_insurance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.employment_insurance') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->employment_insurance ? number_format($employeeSummary->employment_insurance) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.national_pension') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->national_pension ? number_format($employeeSummary->national_pension) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.income_tax') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->income_tax ? number_format($employeeSummary->income_tax) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.local_income_tax') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->local_income_tax ? number_format($employeeSummary->local_income_tax) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.other_deductions') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->other_deductions ? number_format($employeeSummary->other_deductions) : '-' }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">{{ __('employee_summary.table.total_deductions') }}:</div>
                    <div class="info-value currency">{{ $employeeSummary->total_deductions ? number_format($employeeSummary->total_deductions) : '-' }}</div>
                </div>
                
                <div class="info-row border-top pt-2 mt-2">
                    <div class="info-label"><strong>{{ __('employee_summary.table.net_payment') }}:</strong></div>
                    <div class="info-value currency"><strong>{{ $employeeSummary->net_payment ? number_format($employeeSummary->net_payment) : '-' }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Salary Records -->
        <div class="col-md-6 mb-4">
            <div class="info-card">
                <h5><i class="bx bx-chart-line me-2"></i>{{ __('employee_summary.salary_records') }}</h5>
                <div class="no-data">
                    {{ __('employee_summary.no_salary_records') }}
                </div>
            </div>
        </div>

        <!-- Attendance/Leave -->
        <div class="col-md-6 mb-4">
            <div class="info-card">
                <h5><i class="bx bx-calendar-check me-2"></i>{{ __('employee_summary.attendance_leave') }}</h5>
                <div class="no-data">
                    {{ __('employee_summary.no_attendance_records') }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

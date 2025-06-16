<?php

return [
    'processing' => [
        'title_main_suffix' => 'Payroll Processing',
        'total_employees_display_short' => 'Total Employees: :count',
        'sms_sent_employees_display' => 'SMS Sent to Employees: :count',
        'excel_upload_prompt_short' => 'Upload your Excel file here.',
    ],
    'actions' => [
        'site_default' => 'Default Site',
        'selected_count_display_short' => ':count selected',
        'selected_count_none_display' => '0 selected',
        'send_sms_selected_short' => 'Send SMS to Selected', // This button triggers the new offcanvas
    ],
    'table' => [
        'header' => [
            'employee_id_short' => 'Emp. ID',
            'department' => 'Department',
            'position' => 'Position',
            'name' => 'Name',
            'work_days_short' => 'Work Days',
            'base_salary_short' => 'Base Salary',
            'allowances_total_short' => 'Allowances',
            'gross_pay_short' => 'Gross Pay',
            'deductions_total_short' => 'Deductions',
            'net_pay_short' => 'Net Pay',
            'remarks' => 'Remarks',
            'sms_sent_status_short' => 'SMS Status',
        ],
    ],
    'offcanvas_details' => [
        'title_suffix' => 'Allowance and Deduction Details',
        'base_salary_label' => 'Base Salary',
        'allowances_section_title' => 'Allowance Details',
        'deductions_section_title' => 'Deduction Details',
        'modify_button' => 'Modify',
        'allowances' => [
            'seniority' => 'Seniority Allowance',
            'position' => 'Position Allowance',
            'job' => 'Job Allowance',
            'overtime' => 'Overtime Allowance',
            'holiday_special_work' => 'Holiday/Special Work Allowance',
            'night_shift' => 'Night Shift Allowance',
            'bonus' => 'Bonus',
            'adjustment' => 'Adjustment Allowance',
            'transportation' => 'Transportation Allowance',
            'meal' => 'Meal Allowance',
            'labor_day' => 'Labor Day Allowance',
            'annual_leave' => 'Annual Leave Allowance',
            'welfare' => 'Welfare Allowance',
            'other' => 'Other Allowances',
        ],
        'deductions' => [
            'health_insurance' => 'Health Insurance',
            'long_term_care_insurance' => 'Long-term Care Insurance',
            'employment_insurance' => 'Employment Insurance',
            'national_pension' => 'National Pension',
            'income_tax' => 'Income Tax',
            'local_income_tax' => 'Local Income Tax',
            'other' => 'Other Deductions',
        ],
    ],
    // New section for SMS Preview Offcanvas
    'offcanvas_sms' => [
        'title' => 'Send Payroll SMS',
        'selected_employees_count' => 'Selected Employees: :count',
        'search_placeholder' => 'Search Employee',
        'employee_list_header_id' => 'Emp. ID',
        'employee_list_header_department' => 'Department',
        'employee_list_header_name' => 'Name',
        'employee_list_header_phone' => 'Phone Number',
        'sms_preview_title' => 'SMS Preview',
        'sms_recipient_label' => 'Recipient',
        'sms_timestamp_today' => 'Today',
        'sms_company_name' => 'PSMC Corp.',
        'sms_intro_line1' => 'Payroll statement sent. March 2025 salary has been paid.',
        'sms_intro_line2' => 'Thank you for your hard work.',
        'sms_link_text' => 'View Payroll Statement Link',
        'sms_payment_date_label' => '[Payment Date]',
        'sms_emp_code_label' => '[Employee Code]',
        'sms_emp_name_label' => '[Employee Name]',
        'sms_statement_title_prefix' => '[March 2025', // Month and Year will be dynamic
        'sms_statement_title_suffix' => 'Payroll Statement]',
        'sms_base_salary_label' => 'Base Salary',
        'sms_seniority_allowance_label' => 'Seniority (Qualification) Allowance',
        'sms_annual_leave_allowance_label' => 'Annual Leave Allowance',
        'sms_total_gross_pay_label' => '▶ Total Gross Pay',
        'sms_health_insurance_label' => 'Health Insurance',
        'sms_long_term_care_insurance_label' => 'Long-term Care Insurance',
        'sms_income_tax_label' => 'Income Tax',
        'sms_local_income_tax_label' => 'Local Income Tax',
        'sms_total_deductions_label' => '▶ Total Deductions',
        'message_template_label' => 'Message Template',
        'sender_number_label' => 'Sender Number',
        'remaining_points_label' => 'Remaining Points',
        'resend_sms_button' => 'Resend SMS',
        'send_sms_button' => 'Send SMS', // For initial send perhaps
    ],

    'payslip' => [
        'page_title' => ':month :year Payslip', // e.g., March 2025 Payslip
        'download_pdf_button' => 'Download PDF',
        'logout_button' => 'Logout',
        'year_label' => 'Year', // For potential actual select, image shows just text
        'month_label' => 'Month', // For potential actual select, image shows just text
        'payslip_main_title' => ':year :month Payslip', // e.g., 2025년 3월 급여명세서 -> 2025 March Payslip
        'employee_id_label' => 'Employee ID',
        'name_label' => 'Name',
        'payment_date_label' => 'Payment Date',
        'net_pay_amount_label' => 'Net Amount Received', // 실수령액
        'earnings_section_title' => 'Earnings Details', // 지급내역
        'deductions_section_title' => 'Deduction Details', // 공제내역
        'regular_earnings_group' => 'Regular Earnings', // 정기적지급
        'irregular_earnings_group' => 'Irregular Earnings, Incentives, Other Payments', // 부정기적지급 인센티브 기타지급
        'total_label' => 'Total', // 합계

        // Specific Earning Items
        'earning_items' => [
            'base_salary' => 'Base Salary', // 기본급
            'position_allowance' => 'Position Allowance', // 직책수당
            'meal_allowance' => 'Meal Allowance', // 식대
            'night_shift_allowance' => 'Night Shift Allowance', // 야간수당
            // Add more if other empty rows are for specific, common items
        ],

        // Specific Deduction Items
        'deduction_items' => [
            'health_insurance' => 'Health Insurance', // 건강보험
            'long_term_care_insurance' => 'Long-term Care Insurance', // 장기요양보험료
            'employment_insurance' => 'Employment Insurance', // 고용보험
            'national_pension' => 'National Pension', // 국민연금
            'income_tax' => 'Income Tax', // 소득세
            'local_income_tax' => 'Local Income Tax', // 지방소득세
            'other_deductions' => 'Other Deductions', // 기타공제
             // Add more if other empty rows are for specific, common items
        ],
    ],

    // CRUD Operations
    'created_successfully' => 'Payroll record created successfully',
    'updated_successfully' => 'Payroll record updated successfully',
    'deleted_successfully' => 'Payroll record deleted successfully',
    'imported_successfully' => 'Payroll data imported successfully',
    'creation_failed' => 'Failed to create payroll record',
    'update_failed' => 'Failed to update payroll record',
    'deletion_failed' => 'Failed to delete payroll record',
    'import_failed' => 'Failed to import payroll data',
    'bulk_deleted_successfully' => ':count payroll records deleted successfully',
    'bulk_deletion_failed' => 'Failed to delete selected payroll records',
    'sms_status_updated' => 'SMS status updated for :count records',
    'sms_status_update_failed' => 'Failed to update SMS status',
    'delete_confirmation' => 'Are you sure you want to delete this payroll record?',
    'bulk_delete_confirmation' => 'Are you sure you want to delete :count payroll records?',
    'select_records_to_delete' => 'Please select records to delete',
    'select_records_for_sms' => 'Please select records to update SMS status',
    'select_action' => 'Select an action for this payroll record',

    // Modal Form Labels
    'create_new' => 'Create New Payroll',
    'edit_payroll' => 'Edit Payroll',
    'employee_id' => 'Employee ID',
    'employee_name' => 'Employee Name',
    'department' => 'Department',
    'position' => 'Position',
    'phone_number' => 'Phone Number',
    'work_days' => 'Work Days',
    'base_salary' => 'Base Salary',
    'gross_pay' => 'Gross Pay',
    'net_pay' => 'Net Pay',
    'remarks' => 'Remarks',
    
    // Enhanced Modal Labels
    'select_employee' => 'Select Employee',
    'select_employee_placeholder' => 'Choose an employee...',
    'employee_selection_help' => 'Select an employee to auto-populate their information',
    'allowances_detail' => 'Allowance Details',
    'deductions_detail' => 'Deduction Details',
    'select_allowance_type' => 'Select allowance type',
    'select_deduction_type' => 'Select deduction type',
    'add_allowance' => 'Add Allowance',
    'add_deduction' => 'Add Deduction',
    'total_allowances' => 'Total Allowances',
    'total_deductions' => 'Total Deductions',
    'amount' => 'Amount',
    
    // Import functionality
    'import_title' => 'Import Payroll Data',
    'import_description' => 'Upload an Excel or CSV file to import payroll data',
    'select_file' => 'Select File',
    'supported_formats' => 'Supported formats: Excel (.xlsx, .xls) and CSV (.csv)',
    'import_button' => 'Import Data',
    'import_validation_error' => 'Please select a file to import',
];
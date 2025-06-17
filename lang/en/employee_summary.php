<?php

return [
    'title' => 'Employee Summary',
    'import_description' => 'Upload your Employee Summary Excel/CSV file here.',
    'select_file' => 'Select File',
    'download_template' => 'Download Template',
    'importing' => 'Importing...',
    'please_wait' => 'Please wait while we process your file.',
    'import_successful_title' => 'Import Successful',
    'import_successful' => ':count records imported successfully!',
    'import_failed' => 'Import failed. Please check your file format.',
    'delete_all' => 'Delete All',
    'confirm_delete_all' => 'Are you sure?',
    'delete_all_warning' => 'This will permanently delete all employee summary records. This action cannot be undone.',
    'deleted_successfully' => 'Records Deleted',
    'delete_failed' => 'Failed to delete records',
    'deleted_all_successful' => ':count records deleted successfully!',
    'delete_all_failed' => 'Failed to delete all records',
    
    // Statistics
    'total_records' => 'Total Records: :count',
    'latest_import' => 'Latest Import: :date',
    'total_employees' => 'Total Employees',
    'total_base_salary' => 'Total Base Salary',
    'total_net_payment' => 'Total Net Payment', 
    'avg_work_days' => 'Avg Work Days',
    'showing_records' => 'Showing :count of :total records',
    
    // Table headers
    'table' => [
        'employee_id' => 'Employee ID',
        'name' => 'Name',
        'company' => 'Company',
        'position' => 'Position',
        'age' => 'Age',
        'work_days' => 'Work Days',
        'base_salary' => 'Base Salary',
        'total_earnings' => 'Total Earnings',
        'total_deductions' => 'Total Deductions',
        'net_payment' => 'Net Payment',
        'contact' => 'Contact',
        'join_date' => 'Join Date',
        'imported_at' => 'Imported At',
    ],
    
    // Empty state
    'no_records' => 'No Employee Summary Records',
    'no_records_description' => 'Import your first Employee Summary file to get started.',
    'import_first_file' => 'Import First File',
    
    // CRUD messages
    'created_successfully' => 'Employee summary created successfully!',
    'updated_successfully' => 'Employee summary updated successfully!',
    'deleted_successfully' => 'Employee summary deleted successfully!',
];

<?php

// Test script to verify payroll functionality
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Employee;
use App\Models\Payroll;

echo "=== Payroll System Test ===\n";

// Test 1: Check if employees exist
echo "\n1. Testing Employee Data:\n";
$employees = Employee::all();
echo "Found {$employees->count()} employees:\n";
foreach ($employees as $employee) {
    echo "  - {$employee->employee_id}: {$employee->name} ({$employee->work_location})\n";
}

// Test 2: Check if payroll records exist
echo "\n2. Testing Payroll Data:\n";
$payrolls = Payroll::all();
echo "Found {$payrolls->count()} payroll records\n";

// Test 3: Test employee selection format for JSON response
echo "\n3. Testing Employee JSON Format:\n";
$employeesForJson = Employee::select('id', 'employee_id', 'name', 'work_location as department', 'position', 'contact as phone_number', 'base_salary')
                           ->orderBy('name')
                           ->get();

echo "Employee JSON format:\n";
echo json_encode([
    'success' => true,
    'employees' => $employeesForJson->toArray()
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "\n\n=== Test Complete ===\n";

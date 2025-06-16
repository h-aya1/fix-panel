<?php
/**
 * Simple test script to verify Payroll CRUD operations
 * Run this script to test the payroll endpoints
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Create a basic test function
function testPayrollCRUD() {
    $baseUrl = 'http://127.0.0.1:8001';
    
    // Test data for creating a payroll record
    $testPayrollData = [
        'employee_id' => 'TEST001',
        'name' => 'Test Employee',
        'department' => 'Test Department',
        'position' => 'Test Position',
        'phone_number' => '010-1234-5678',
        'work_days' => 22,
        'base_salary' => 2500000,
        'allowances' => 300000,
        'deductions' => 250000,
        'remarks' => 'Test payroll record'
    ];
    
    echo "Testing Payroll CRUD Operations...\n";
    echo "==================================\n\n";
    
    // Test 1: GET /payrolls (List all payrolls)
    echo "1. Testing GET /payrolls...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/payrolls');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✓ GET /payrolls successful. Found " . (is_array($data) ? count($data) : 0) . " records.\n\n";
    } else {
        echo "✗ GET /payrolls failed. HTTP Code: $httpCode\n\n";
    }
    
    // Test 2: POST /payrolls (Create new payroll)
    echo "2. Testing POST /payrolls (Create)...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/payrolls');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testPayrollData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest',
        'Content-Type: application/x-www-form-urlencoded'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 || $httpCode === 201) {
        echo "✓ POST /payrolls successful. HTTP Code: $httpCode\n";
        $responseData = json_decode($response, true);
        if (isset($responseData['success']) && $responseData['success']) {
            echo "✓ Payroll record created successfully.\n\n";
        } else {
            echo "⚠ Response received but may not be successful: " . $response . "\n\n";
        }
    } else {
        echo "✗ POST /payrolls failed. HTTP Code: $httpCode\n";
        echo "Response: " . $response . "\n\n";
    }
    
    echo "Test completed. Check the browser interface for visual confirmation.\n";
    echo "You can also manually test the Create and Import modals in the browser.\n";
}

// Run the test
testPayrollCRUD();

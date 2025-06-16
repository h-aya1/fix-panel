<!DOCTYPE html>
<html>
<head>
    <title>Payroll System Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { color: green; }
        .error { color: red; }
        .result { background: #f5f5f5; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Payroll System Functionality Test</h1>
    
    <div class="test-section">
        <h3>Test 1: Employee Data Loading</h3>
        <button onclick="testEmployees()">Test Get Employees</button>
        <div id="employees-result" class="result"></div>
    </div>
    
    <div class="test-section">
        <h3>Test 2: Single Employee Data</h3>
        <input type="number" id="employee-id" placeholder="Enter Employee ID" value="1">
        <button onclick="testSingleEmployee()">Test Get Single Employee</button>
        <div id="single-employee-result" class="result"></div>
    </div>
    
    <div class="test-section">
        <h3>Test 3: Payroll Creation</h3>
        <button onclick="testPayrollCreation()">Test Create Payroll</button>
        <div id="payroll-creation-result" class="result"></div>
    </div>

    <script>
        function testEmployees() {
            $('#employees-result').html('Loading...');
            
            $.ajax({
                url: '/payrolls/employees',
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success && response.employees) {
                        let html = '<div class="success">✓ Successfully loaded ' + response.employees.length + ' employees:</div>';
                        response.employees.forEach(function(emp) {
                            html += '<div>- ' + emp.employee_id + ': ' + emp.name + ' (' + emp.department + ')</div>';
                        });
                        $('#employees-result').html(html);
                    } else {
                        $('#employees-result').html('<div class="error">✗ Unexpected response format</div>');
                    }
                },
                error: function(xhr) {
                    $('#employees-result').html('<div class="error">✗ Error: ' + xhr.status + ' - ' + xhr.responseText + '</div>');
                }
            });
        }

        function testSingleEmployee() {
            const employeeId = $('#employee-id').val();
            $('#single-employee-result').html('Loading...');
            
            $.ajax({
                url: '/payrolls/employees/' + employeeId,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success && response.employee) {
                        let html = '<div class="success">✓ Successfully loaded employee data:</div>';
                        html += '<div>Employee ID: ' + response.employee.employee_id + '</div>';
                        html += '<div>Name: ' + response.employee.name + '</div>';
                        html += '<div>Department: ' + response.employee.department + '</div>';
                        html += '<div>Position: ' + response.employee.position + '</div>';
                        html += '<div>Phone: ' + response.employee.phone_number + '</div>';
                        html += '<div>Base Salary: ' + response.employee.base_salary + '</div>';
                        $('#single-employee-result').html(html);
                    } else {
                        $('#single-employee-result').html('<div class="error">✗ Employee not found or unexpected response</div>');
                    }
                },
                error: function(xhr) {
                    $('#single-employee-result').html('<div class="error">✗ Error: ' + xhr.status + ' - ' + xhr.responseText + '</div>');
                }
            });
        }

        function testPayrollCreation() {
            $('#payroll-creation-result').html('Testing...');
            
            const testData = {
                employee_id: 'EMP001',
                name: '김영희',
                department: '인사팀',
                position: '대리',
                phone_number: '010-1234-5678',
                work_days: 22,
                base_salary: 3000000,
                allowance_items: [
                    { type: 'seniority', amount: 300000 },
                    { type: 'transportation', amount: 200000 }
                ],
                deduction_items: [
                    { type: 'health_insurance', amount: 150000 },
                    { type: 'income_tax', amount: 200000 }
                ],
                remarks: 'Test payroll creation'
            };
            
            $.ajax({
                url: '/payrolls',
                type: 'POST',
                data: testData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        $('#payroll-creation-result').html('<div class="success">✓ Payroll created successfully!</div><div>Response: ' + JSON.stringify(response.data, null, 2) + '</div>');
                    } else {
                        $('#payroll-creation-result').html('<div class="error">✗ Failed to create payroll: ' + response.message + '</div>');
                    }
                },
                error: function(xhr) {
                    $('#payroll-creation-result').html('<div class="error">✗ Error: ' + xhr.status + ' - ' + xhr.responseText + '</div>');
                }
            });
        }
    </script>
</body>
</html>

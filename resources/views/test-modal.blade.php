<!DOCTYPE html>
<html>
<head>
    <title>Payroll Modal Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { color: green; }
        .error { color: red; }
        .result { background: #f5f5f5; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Payroll Modal Functionality Test</h1>
    
    <div class="test-section">
        <h3>Test Modal Creation</h3>
        <button class="btn btn-primary" onclick="openCreatePayrollModal()">Test Create Modal</button>
        <div id="modal-test-result" class="result"></div>
    </div>

    <!-- Modal Structure (Simplified for Testing) -->
    <div class="modal fade" id="payrollModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="payrollModalTitle">Create New Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="payrollForm" class="row g-3">
                        <!-- Employee Selection -->
                        <div class="col-12">
                            <label for="modalEmployeeSelect" class="form-label">Select Employee</label>
                            <select id="modalEmployeeSelect" class="form-select" required>
                                <option value="">Choose an employee...</option>
                            </select>
                        </div>
                        
                        <!-- Employee Information -->
                        <div class="col-md-6">
                            <label for="modalEmployeeId" class="form-label">Employee ID</label>
                            <input type="text" id="modalEmployeeId" name="employee_id" class="form-control" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label for="modalEmployeeName" class="form-label">Employee Name</label>
                            <input type="text" id="modalEmployeeName" name="name" class="form-control" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label for="modalDepartment" class="form-label">Department</label>
                            <input type="text" id="modalDepartment" name="department" class="form-control" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label for="modalPosition" class="form-label">Position</label>
                            <input type="text" id="modalPosition" name="position" class="form-control" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label for="modalPhoneNumber" class="form-label">Phone Number</label>
                            <input type="text" id="modalPhoneNumber" name="phone_number" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="modalWorkDays" class="form-label">Work Days</label>
                            <input type="number" id="modalWorkDays" name="work_days" class="form-control" min="0" max="31" required>
                        </div>
                        
                        <!-- Base Salary -->
                        <div class="col-12">
                            <label for="modalBaseSalary" class="form-label">Base Salary</label>
                            <input type="number" id="modalBaseSalary" name="base_salary" class="form-control" min="0" step="0.01" required>
                        </div>

                        <!-- Allowances Section -->
                        <div class="col-12">
                            <hr>
                            <h6 class="mb-3">Allowance Details</h6>
                            <div id="allowancesContainer">
                                <div class="allowance-item row mb-2">
                                    <div class="col-md-6">
                                        <select class="form-select allowance-type" name="allowance_type[]">
                                            <option value="">Select allowance type</option>
                                            <option value="seniority">Seniority Allowance</option>
                                            <option value="overtime">Overtime Allowance</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" class="form-control allowance-amount" name="allowance_amount[]" placeholder="Amount" min="0" step="0.01">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-allowance" style="display: none;">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="addAllowanceBtn" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>Add Allowance
                            </button>
                            <div class="mt-2">
                                <strong>Total Allowances: <span id="totalAllowances">0</span></strong>
                            </div>
                        </div>

                        <!-- Deductions Section -->
                        <div class="col-12">
                            <hr>
                            <h6 class="mb-3">Deduction Details</h6>
                            <div id="deductionsContainer">
                                <div class="deduction-item row mb-2">
                                    <div class="col-md-6">
                                        <select class="form-select deduction-type" name="deduction_type[]">
                                            <option value="">Select deduction type</option>
                                            <option value="health_insurance">Health Insurance</option>
                                            <option value="income_tax">Income Tax</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" class="form-control deduction-amount" name="deduction_amount[]" placeholder="Amount" min="0" step="0.01">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-deduction" style="display: none;">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="addDeductionBtn" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-plus me-1"></i>Add Deduction
                            </button>
                            <div class="mt-2">
                                <strong>Total Deductions: <span id="totalDeductions">0</span></strong>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="col-12">
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="modalGrossPay" class="form-label">Gross Pay</label>
                                    <input type="number" id="modalGrossPay" name="gross_pay" class="form-control" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="modalNetPay" class="form-label">Net Pay</label>
                                    <input type="number" id="modalNetPay" name="net_pay" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label for="modalRemarks" class="form-label">Remarks</label>
                            <textarea id="modalRemarks" name="remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="savePayrollBtn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Test all required functions
            const functions = [
                'updateRemoveButtonsVisibility',
                'addAllowanceItem', 
                'addDeductionItem',
                'calculateTotals',
                'resetAllowanceDeductionContainers',
                'loadEmployees',
                'loadEmployeeData',
                'clearEmployeeFields'
            ];
            
            let result = '<h4>Function Availability Test:</h4>';
            functions.forEach(func => {
                if (typeof window[func] === 'function') {
                    result += `<div class="success">✓ ${func} - Defined</div>`;
                } else {
                    result += `<div class="error">✗ ${func} - Missing</div>`;
                }
            });
            
            $('#modal-test-result').html(result);
            
            // Test event handlers
            $('#addAllowanceBtn').on('click', function() {
                console.log('Add allowance clicked');
                if (typeof addAllowanceItem === 'function') {
                    addAllowanceItem();
                    $('#modal-test-result').append('<div class="success">✓ addAllowanceItem executed successfully</div>');
                } else {
                    $('#modal-test-result').append('<div class="error">✗ addAllowanceItem function not found</div>');
                }
            });
            
            $('#addDeductionBtn').on('click', function() {
                console.log('Add deduction clicked');
                if (typeof addDeductionItem === 'function') {
                    addDeductionItem();
                    $('#modal-test-result').append('<div class="success">✓ addDeductionItem executed successfully</div>');
                } else {
                    $('#modal-test-result').append('<div class="error">✗ addDeductionItem function not found</div>');
                }
            });
            
            // Test calculations
            $(document).on('input', '.allowance-amount, .deduction-amount, #modalBaseSalary', function() {
                if (typeof calculateTotals === 'function') {
                    calculateTotals();
                }
            });
        });
        
        // Simplified versions of functions for testing
        function updateRemoveButtonsVisibility() {
            const allowanceItems = $('#allowancesContainer .allowance-item');
            allowanceItems.find('.remove-allowance').toggle(allowanceItems.length > 1);
            
            const deductionItems = $('#deductionsContainer .deduction-item');
            deductionItems.find('.remove-deduction').toggle(deductionItems.length > 1);
        }
        
        function addAllowanceItem() {
            const container = $('#allowancesContainer');
            const newItem = $(`
                <div class="allowance-item row mb-2">
                    <div class="col-md-6">
                        <select class="form-select allowance-type">
                            <option value="">Select allowance type</option>
                            <option value="seniority">Seniority Allowance</option>
                            <option value="overtime">Overtime Allowance</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control allowance-amount" placeholder="Amount" min="0" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-allowance">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                </div>
            `);
            container.append(newItem);
            updateRemoveButtonsVisibility();
        }
        
        function addDeductionItem() {
            const container = $('#deductionsContainer');
            const newItem = $(`
                <div class="deduction-item row mb-2">
                    <div class="col-md-6">
                        <select class="form-select deduction-type">
                            <option value="">Select deduction type</option>
                            <option value="health_insurance">Health Insurance</option>
                            <option value="income_tax">Income Tax</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control deduction-amount" placeholder="Amount" min="0" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-deduction">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                </div>
            `);
            container.append(newItem);
            updateRemoveButtonsVisibility();
        }
        
        function calculateTotals() {
            let totalAllowances = 0;
            $('#allowancesContainer .allowance-amount').each(function() {
                const amount = parseFloat($(this).val()) || 0;
                totalAllowances += amount;
            });
            $('#totalAllowances').text(totalAllowances.toLocaleString());
            
            let totalDeductions = 0;
            $('#deductionsContainer .deduction-amount').each(function() {
                const amount = parseFloat($(this).val()) || 0;
                totalDeductions += amount;
            });
            $('#totalDeductions').text(totalDeductions.toLocaleString());
            
            const baseSalary = parseFloat($('#modalBaseSalary').val()) || 0;
            const grossPay = baseSalary + totalAllowances;
            const netPay = grossPay - totalDeductions;
            
            $('#modalGrossPay').val(grossPay.toFixed(2));
            $('#modalNetPay').val(netPay.toFixed(2));
        }
        
        function resetAllowanceDeductionContainers() {
            $('#allowancesContainer').html(`
                <div class="allowance-item row mb-2">
                    <div class="col-md-6">
                        <select class="form-select allowance-type">
                            <option value="">Select allowance type</option>
                            <option value="seniority">Seniority Allowance</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control allowance-amount" placeholder="Amount" min="0" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-allowance" style="display: none;">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                </div>
            `);
            
            $('#deductionsContainer').html(`
                <div class="deduction-item row mb-2">
                    <div class="col-md-6">
                        <select class="form-select deduction-type">
                            <option value="">Select deduction type</option>
                            <option value="health_insurance">Health Insurance</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control deduction-amount" placeholder="Amount" min="0" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-deduction" style="display: none;">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                </div>
            `);
        }
        
        function loadEmployees() { /* Mock function for testing */ }
        function loadEmployeeData(id) { /* Mock function for testing */ }
        function clearEmployeeFields() { /* Mock function for testing */ }
        
        window.openCreatePayrollModal = function() {
            const modal = new bootstrap.Modal($('#payrollModal')[0]);
            resetAllowanceDeductionContainers();
            calculateTotals();
            updateRemoveButtonsVisibility();
            modal.show();
        };
        
        // Remove item handlers
        $(document).on('click', '.remove-allowance', function() {
            $(this).closest('.allowance-item').remove();
            calculateTotals();
            updateRemoveButtonsVisibility();
        });
        
        $(document).on('click', '.remove-deduction', function() {
            $(this).closest('.deduction-item').remove();
            calculateTotals();
            updateRemoveButtonsVisibility();
        });
    </script>
</body>
</html>

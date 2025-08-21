@extends('layouts.app')

@section('title', 'Employee Summaries')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('page-style')
  <link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.base.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.bootstrap.css') }}" type="text/css" />
  <style>
    body {
        font-family: -apple-system, CryillicMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 0.9rem;
        background-color: #f4f6f9;
    }
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1.5rem;
    }
    .header-main-title { font-size: 1.65rem; font-weight: 600; }
    
    /* Main Content Area */
    .grid-wrapper {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1.5rem;
    }

    /* Action Bar Styles */
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .action-bar .left-actions, .action-bar .right-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .search-bar {
        width: 250px;
    }
    .total-count {
        font-weight: 500;
        color: #495057;
    }
    /* Specific style for Send SMS button */
    #sendSmsBtn {
        background-color: transparent;
        border: 1px solid #ced4da;
        color: #212529;
    }
    #sendSmsBtn:hover {
        background-color: #f8f9fa;
    }
    
    /* Grid Customizations */
    .jqx-grid-cell {
        cursor: pointer;
    }
    .status-badge {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
        color: #fff;
    }
    .status-working { background-color: #28a745; }
    .status-resigning { background-color: #ffc107; color: #212529; }
    .제status-on-leave { background-color: #17a2b8; }
    .sms-status-sent { color: #28a745; font-size: 1.2rem;}
    .sms-status-pending { color: #dc3545; font-size: 1.2rem;}

    /* Hidden input */
    #fileInput {
        display: none;
    }

    /* Side Panel for Editing Allowance/Deduction */
    .side-panel {
        position: fixed;
        top: 0;
        right: -450px; /* Initially hidden */
        width: 450px;
        height: 100%;
        background: #fff;
        box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        z-index: 1055;
        transition: right 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
    }
    .side-panel.open {
        right: 0;
    }
    .panel-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .panel-header h5 { margin: 0; }
    .panel-body {
        padding: 1.5rem;
        overflow-y: auto;
        flex-grow: 1;
    }
    .panel-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #dee2e6;
        background-color: #f8f9fa;
        text-align: right;
    }
    .details-section { margin-bottom: 1.5rem; }
    .details-section h6 { font-weight: 600; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }
    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    .detail-item label { color: #6c757d; }
    .detail-item input {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
        width: 150px;
        text-align: right;
    }
    
    /* SMS Preview Modal Styles */
    .sms-modal-body {
        display: flex;
        gap: 1rem;
        padding: 1rem;
    }

    .sms-recipients-section {
        flex: 0 0 53%;
        display: flex;
        flex-direction: column;
    }

    .recipient-table-wrapper {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        height: 400px; /* Reduced height */
        overflow-y: auto;
    }

    .recipient-table-wrapper table {
        margin-bottom: 0;
    }

    .recipient-table-wrapper tbody tr {
        cursor: pointer;
    }

    .recipient-table-wrapper tbody tr.selected {
        background-color: #ffeeba; /* A light yellow to indicate selection */
    }

    .sms-preview-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .iphone-mockup {
        width: 250px; /* Scaled up for better visibility */
        height: 530px; /* Scaled up for better visibility */
        margin: 0 auto;
        border: 5px solid #111;
        border-radius: 45px;
        background: #111;
        position: relative;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        overflow: hidden;
    }

    /* Screen area */
    .iphone-screen {
        width: 100%;
        height: 100%;
        background: #fff;
        border-radius: 32px;
        overflow: hidden;
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
    }

    /* Notch */
    .iphone-notch { /* Dynamic Island */
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        width: 90px;
        height: 25px;
        background: #111;
        border-radius: 12px;
        z-index: 5;
    }

    /* Status Bar */
    .iphone-status-bar {
        position: absolute;
        top: 12px;
        left: 13px;
        right: 13px;
        padding: 0 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        font-weight: 500;
        color: #000;
        z-index: 6;
    }

    .status-icons {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .sms-message-header {
        position: absolute;
        top: 40px; /* Below status bar */
        left: 0;
        right: 0;
        display: flex;
        align-items: center;
        justify-content: center; /* Center the recipient info */
        padding: 4px 15px; /* Reduced padding */
        border-bottom: 1px solid #e5e5e5;
        background: #f7f7f7;
    }

    .sms-message-header .back-link {
        position: absolute;
        left: 15px;
        font-size: 0.8rem;
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }

    .recipient-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .recipient-avatar {
        width: 30px; /* Reduced size */
        height: 30px; /* Reduced size */
        background-color: #d8d8d8;
        border-radius: 50%;
        margin-bottom: 3px; /* Reduced margin */
        position: relative;
    }
    .recipient-avatar::before {
        content: '';
        position: absolute;
        top: 6px; /* Adjusted position */
        left: 50%;
        transform: translateX(-50%);
        width: 12px; /* Adjusted size */
        height: 12px; /* Adjusted size */
        background: #fff;
        border-radius: 50%;
    }
     .recipient-avatar::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 21px; /* Adjusted size */
        height: 11px; /* Adjusted size */
        background: #fff;
        border-top-left-radius: 11px; /* Adjusted size */
        border-top-right-radius: 11px; /* Adjusted size */
    }

    .recipient-name {
        font-size: 0.75rem;
        color: #888;
    }

 
    .iphone-status-bar .icons {
        font-size: 0.7rem;
    }

    /* Side Buttons */
    .iphone-buttons {
        position: absolute;
        left: -5px;
        top: 100px;
        width: 5px;
        height: 50px;
        border-radius: 2px;
        background: #333;
        box-shadow: 1px 0 2px rgba(0,0,0,0.3);
    }

    .sms-content {
        padding: 15px 10px;
        font-size: 0.7rem;
        white-space: pre-wrap;
        line-height: 1.4;
        flex-grow: 1;
        overflow-y: auto; /* Allow scrolling if content overflows */
        color: #000;
        margin-top: 95px; /* Reduced margin to bring content up */
        display: flex;
        flex-direction: column;
    }

    .sms-timestamp {
        text-align: center;
        font-size: 0.4rem;
        color: #888;
        margin-top: 0px;
        margin-bottom: 6px;
        padding: 2px 8px;
        background-color: #f0f0f0;
        border-radius: 10px;
        align-self: center;
    }

    .sms-bubble {
        background-color: #E9E9EB;
        border-radius: 18px;
        padding: 0px 14px;
        max-width: 97%;
        margin-bottom: 5px;
        font-size: 0.72rem;
        line-height: 1.3;
        /* word-wrap: break-word; */
        color: #000;
    }

    .sms-bubble strong {
        font-weight: 500;
    }

    .sms-bubble a {
        color: #007bff;
        text-decoration: none;
    }

    .sms-details-area {
        width: 100%;
        margin-top: 1.5rem;
        padding: 0 1rem;
    }

    .sms-message-box {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.75rem;
        font-size: 0.85rem;
        background-color: #f8f9fa;
        min-height: 100px;
    }

    .sms-sending-info {
        font-size: 0.9rem;
    }

    .sms-sending-info .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.35rem 0;
    }

    .info-item .info-label {
        font-weight: 500;
        color: #555;
    }

    .info-item .info-value {
        background-color: #e9e9eb;
        border-radius: 10px;
        padding: 0.4rem 0.8rem;
        font-weight: 500;
        min-width: 120px;
        text-align: center;
    }

    .additional-message-area {
        margin-bottom: 1rem;
    }

    .additional-message-area label {
        font-weight: 500;
        color: #555;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    #adminMessageInput.form-control {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        background-color: #f9f9f9;
        padding: 0.75rem;
        font-size: 0.85rem;
        resize: none;
    }

    #adminMessageInput.form-control:focus {
        background-color: #fff;
        border-color: #64d4b9;
        box-shadow: 0 0 0 0.2rem rgba(100, 212, 185, 0.25);
    }

    .recipient-table-wrapper .table {
        border: 1px solid #e0e0e0;
    }

    .recipient-table-wrapper .table thead th {
        background-color: #e9e9eb;
        font-weight: 500;
        font-size: 0.8rem;
        padding: 0.5rem;
        text-align: center;
        border: 1px solid #e0e0e0;
    }

    .recipient-table-wrapper .table tbody td {
        font-size: 0.8rem;
        padding: 0.5rem;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #e0e0e0;
    }

    .recipient-table-wrapper .table tbody tr.selected {
        background-color: #ffebee;
    }

    /* Hidden input */
    #fileInput {
        display: none;
    }
  </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="header-main-title">Employee Summaries</h1>
        </div>
        <div>
            <select class="form-select form-select-sm d-inline-block w-auto">
                <option>2025</option>
            </select>
            <select class="form-select form-select-sm d-inline-block w-auto">
                <option>March</option>
            </select>
        </div>
    </div>

    <!-- Grid Wrapper -->
    <div class="grid-wrapper">
        <!-- Action Bar -->
        <div class="action-bar">
            <div class="left-actions">
                <span id="totalCount" class="total-count">Total: 0</span>
                <span id="selectedCount" class="text-muted ms-3"></span>
                <input type="text" id="nameSearch" class="form-control form-control-sm search-bar" placeholder="Search (name)">
            </div>
            <div class="right-actions">
                <button class="btn btn-secondary btn-sm" id="importExcelBtn"><i class="bx bx-upload me-1"></i> Import excel</button>
                <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" />
                <button class="btn btn-outline-danger btn-sm" id="deleteSelectedBtn"><br><i class="bx bx-trash me-1"></i> Delete selected user</button>
                <button class="btn btn-sm" id="sendSmsBtn"><i class="bx bx-send me-1"></i> Send a sms</button>
            </div>
        </div>

        <!-- Grid Container -->
        <div id="employeeGrid"></div>
    </div>
</div>

<!-- Side Panel for Allowance & Deduction -->
<div class="side-panel" id="allowanceDeductionPanel">
    <div class="panel-header">
        <h5 class="mb-0">
            <span id="panelEmployeeId"></span> <span id="panelEmployeeName"></span> - Allowance & Deduction Details
        </h5>
        <button type="button" class="btn-close" id="closePanelBtn" aria-label="Close"></button>
    </div>
    <div class="panel-body">
        <form id="detailsForm">
            <input type="hidden" id="panelEditEmployeeId" name="id">
            <div class="details-section">
                <div class="detail-item">
                    <label for="base_salary" class="fw-bold">Base Salary</label>
                    <input type="text" id="base_salary" name="base_salary" class="form-control form-control-sm w-50 text-end">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="details-section">
                        <h6>Allowance Breakdown (<span id="totalAllowance">0</span>)</h6>
                        <div class="detail-item"><label>Early Duty Allowance</label><input type="text" name="early_duty_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Position Allowance</label><input type="text" name="position_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Duty Allowance</label><input type="text" name="duty_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Overtime Allowance</label><input type="text" name="overtime_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Holiday Work Allowance</label><input type="text" name="holiday_work_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Night Shift Allowance</label><input type="text" name="night_shift_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Bonus</label><input type="text" name="bonus" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Adjustment Allowance</label><input type="text" name="adjustment_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Transportation Allowance</label><input type="text" name="transportation_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Meal Allowance</label><input type="text" name="meal_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Labor Day Allowance</label><input type="text" name="labor_day_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Annual Leave Allowance</label><input type="text" name="annual_leave_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Welfare Allowance</label><input type="text" name="welfare_allowance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Other Allowances</label><input type="text" name="other_allowances" class="form-control form-control-sm w-50 text-end"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="details-section">
                        <h6>Deduction Breakdown (<span id="totalDeduction">0</span>)</h6>
                        <div class="detail-item"><label>Health Insurance</label><input type="text" name="health_insurance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Long-Term Care Insurance</label><input type="text" name="long_term_care_insurance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Employment Insurance</label><input type="text" name="employment_insurance" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>National Pension</label><input type="text" name="national_pension" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Income Tax</label><input type="text" name="income_tax" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Local Income Tax</label><input type="text" name="local_income_tax" class="form-control form-control-sm w-50 text-end"></div>
                        <div class="detail-item"><label>Other Deductions</label><input type="text" name="other_deductions" class="form-control form-control-sm w-50 text-end"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="panel-footer">
        <button class="btn btn-primary w-100" id="saveDetailsBtn">Edit</button>
    </div>
</div>

<!-- Send SMS Modal -->
<div class="modal fade" id="smsPreviewModal" tabindex="-1" aria-labelledby="smsPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 700px; max-height: 500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="smsPreviewModalLabel">Send SMS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body sms-modal-body">
                <!-- Left side: Employee List -->
                <div class="sms-recipients-section">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>Selected: <span id="smsSelectedCount">0</span>명</h6>
                        <input type="text" class="form-control form-control-sm w-50" id="smsRecipientSearch" placeholder="search (name, number, ID)">
                    </div>
                    <div class="recipient-table-wrapper">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Department</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                </tr>
                            </thead>
                            <tbody id="smsRecipientList">
                                <!-- Recipient rows will be populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right side: SMS Preview -->
                <div class="sms-preview-section">
                    <div class="iphone-mockup">
                        <div class="iphone-notch"></div>
                        <div class="iphone-screen">
                            <div class="iphone-status-bar">
                                <span class="time">9:41</span>
                                <div class="status-icons">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M2 16.55C2 16.8 2.2 17 2.45 17h.1c.25 0 .45-.2.45-.45V14.4c0-.25-.2-.45-.45-.45h-.1c-.25 0-.45.2-.45.45v2.15zm4.45-3.85C6.45 12.9 6.65 13.1 6.9 13.1h.1c.25 0 .45-.2.45-.45V11.2c0-.25-.2-.45-.45-.45h-.1c-.25 0-.45.2-.45.45v1.45zm4.45-3.85c0 .25.2.45.45.45h.1c.25 0 .45-.2.45-.45V7.35c0-.25-.2-.45-.45-.45h-.1c-.25 0-.45.2-.45.45v1.45zm4.45-3.85c0 .25.2.45.45.45h.1c.25 0 .45-.2.45-.45V3.5c0-.25-.2-.45-.45-.45h-.1c-.25 0-.45.2-.45.45v1.45z"></path></svg>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21,7H3C1.9,7,1,7.9,1,9v6c0,1.1,0.9,2,2,2h18c1.1,0,2-0.9,2-2V9C23,7.9,22.1,7,21,7z M21,15H3V9h18V15z"/><path d="M24,11h-1v2h1c0.6,0,1-0.4,1-1S24.6,11,24,11z"/></svg>
                                </div>
                            </div>
                            <div class="sms-message-header">
                                <a href="#" class="back-link">&lt;</a>
                                <div class="recipient-info">
                                    <div class="recipient-avatar"></div>
                                    <span class="recipient-name" id="smsRecipientName">받는 사람 &gt;</span>
                                </div>
                            </div>
                            <div class="sms-content" id="smsPreviewContent">
                                <!-- SMS preview will be populated by JS -->
                            </div>
                        </div>
                    </div>

                    <div class="sms-details-area">
                        <div class="additional-message-area">
                            <label for="adminMessageInput">Additional Message</label>
                            <textarea class="form-control" id="adminMessageInput" rows="3" placeholder="Enter an optional message..."></textarea>
                        </div>
                        <div class="sms-sending-info">
                            <div class="info-item">
                                <span class="info-label">Send number</span>
                                <strong class="info-value">070-5555-3333</strong>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Remaining point</span>
                                <strong class="info-value" id="remainingPoints">40,080</strong>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                             <button type="button" class="btn btn-lg" id="confirmSendSmsBtn" style="background-color: #64d4b9; color: white; border-radius: 25px; border: none; font-weight: bold;">Send a sms</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script src="{{ asset('jqwidgets/jqxcore.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxdata.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxbuttons.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxscrollbar.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxlistbox.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxdropdownlist.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxmenu.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.selection.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.columnsresize.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.filter.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxgrid.sort.js') }}"></script>
<script src="{{ asset('jqwidgets/jqxcheckbox.js') }}"></script>

<script>
$(document).ready(function() {
    let allEmployeeData = [];
    
    // Initial load
    loadEmployees();

    function loadEmployees(searchTerm = '') {
        $.ajax({
            url: '{{ route('employees.index') }}',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                allEmployeeData = data.map(emp => ({
                    ...emp,
                    total_allowance: emp.total_allowance || 358190,
                    total_deduction: emp.total_deduction || 290950,
                    total_pay: (emp.base_salary || 0) + (emp.total_allowance || 358190),
                    net_pay: ((emp.base_salary || 0) + (emp.total_allowance || 358190)) - (emp.total_deduction || 290950),
                    remarks: emp.remarks || 'Early pension withdrawal, pension deduction excluded',
                    sms_sent: emp.sms_sent !== undefined ? emp.sms_sent : Math.random() > 0.3,
                    employment_status: emp.employment_status || 'Working'
                }));

                let filteredData = allEmployeeData;
                if (searchTerm) {
                    filteredData = allEmployeeData.filter(emp => 
                        emp.name.toLowerCase().includes(searchTerm.toLowerCase())
                    );
                }

                setupGrid(filteredData);
                $('#totalCount').text(`Total: ${filteredData.length} Working: ${filteredData.filter(e => e.employment_status === 'Working').length}`);
            },
            error: function() {
                showAlert('danger', 'Failed to load employee data.');
            }
        });
    }

    function setupGrid(data) {
        const source = { localdata: data, datatype: "array" };
        const dataAdapter = new $.jqx.dataAdapter(source);

        const statusRenderer = (row, column, value) => {
            const statusClass = {
                'working': 'status-working',
                'resigning soon': 'status-resigning',
                'on leave': 'status-on-leave'
            }[value.toLowerCase()] || '';
            return `<div style="margin-top: 5px;"><span class="status-badge ${statusClass}">${value}</span></div>`;
        };

        const smsRenderer = (row, column, value) => {
            const icon = value ? 'bx bx-check-circle sms-status-sent' : 'bx bx-x-circle sms-status-pending';
            return `<div style="text-align: center; margin-top: 5px;"><i class="${icon}"></i></div>`;
        };

        $("#employeeGrid").jqxGrid({
            width: '100%',
            source: dataAdapter,
            columnsresize: true,
            sortable: true,
            selectionmode: 'checkbox',
            columns: [
                { text: 'Employee ID', datafield: 'employee_id', width: 100 },
                { text: 'Company', datafield: 'company', width: 120 },
                { text: 'Position', datafield: 'position', width: 120 },
                { text: 'Name', datafield: 'name', width: 120 },
                { text: 'Work Days', datafield: 'work_days', width: 100, cellsalign: 'right' },
                { text: 'Base Salary', datafield: 'base_salary', width: 120, cellsformat: 'n', cellsalign: 'right' },
                { text: 'Total Allowance', datafield: 'total_allowance', width: 130, cellsformat: 'n', cellsalign: 'right' },
                { text: 'Total Pay', datafield: 'total_pay', width: 120, cellsformat: 'n', cellsalign: 'right' },
                { text: 'Total Deduction', datafield: 'total_deduction', width: 130, cellsformat: 'n', cellsalign: 'right' },
                { text: 'Net Pay', datafield: 'net_pay', width: 120, cellsformat: 'n', cellsalign: 'right' },
                { text: 'Employment Status', datafield: 'employment_status', width: 150, cellsrenderer: statusRenderer },
                { text: 'Remarks', datafield: 'remarks', minwidth: 150 },
                { text: 'SMS Sent', datafield: 'sms_sent', width: 80, cellsrenderer: smsRenderer, align: 'center' }
            ]
        });
    }

    // Search functionality
    $('#nameSearch').on('keyup', function() {
        loadEmployees($(this).val());
    });

    // --- Grid Selection Counter ---
    $("#employeeGrid").on('rowselect rowunselect', function (event) {
        const selectedCount = $("#employeeGrid").jqxGrid('getselectedrowindexes').length;
        $('#selectedCount').text(selectedCount > 0 ? `${selectedCount} selected` : '');
    });

    // --- Allowance/Deduction Side Panel Logic ---
    function formatNumber(num) {
        if (num === null || num === undefined) return '-';
        return new Intl.NumberFormat('en-US').format(num);
    }

    function parseNumber(str) {
        if (typeof str === 'number') return str;
        if (typeof str === 'string') {
            const num = parseFloat(str.replace(/,/g, ''));
            return isNaN(num) ? 0 : num;
        }
        return 0;
    }

    function populateSidePanel(rowData) {
        $('#panelEmployeeId').text(rowData.employee_id);
        $('#panelEmployeeName').text(rowData.name);
        $('#panelEditEmployeeId').val(rowData.id);

        const form = $('#detailsForm');
        form.find('input[type="text"]').val('-'); // Reset all to '-'

        form.find('input[name="base_salary"]').val(formatNumber(rowData.base_salary));
        
        const fields = {
            allowances: ['early_duty_allowance', 'position_allowance', 'duty_allowance', 'overtime_allowance', 'holiday_work_allowance', 'night_shift_allowance', 'bonus', 'adjustment_allowance', 'transportation_allowance', 'meal_allowance', 'labor_day_allowance', 'annual_leave_allowance', 'welfare_allowance', 'other_allowances'],
            deductions: ['health_insurance', 'long_term_care_insurance', 'employment_insurance', 'national_pension', 'income_tax', 'local_income_tax', 'other_deductions']
        };

        let totalAllowance = 0;
        fields.allowances.forEach(field => {
            const value = rowData[field] ? parseNumber(rowData[field]) : 0;
            form.find(`input[name="${field}"]`).val(formatNumber(value));
            totalAllowance += value;
        });

        let totalDeduction = 0;
        fields.deductions.forEach(field => {
            const value = rowData[field] ? parseNumber(rowData[field]) : 0;
            form.find(`input[name="${field}"]`).val(formatNumber(value));
            totalDeduction += value;
        });

        $('#totalAllowance').text(formatNumber(totalAllowance));
        $('#totalDeduction').text(formatNumber(totalDeduction));
    }

    $('#employeeGrid').on('cellclick', function (event) {
        const args = event.args;
        // Prevent opening panel on checkbox click
        if (args.columnindex === 0 || args.rowindex < 0) {
            return;
        }
        const rowData = args.row.bounddata;
        if (rowData) {
            populateSidePanel(rowData);
            $('#allowanceDeductionPanel').addClass('open');
        }
    });

    $('#closePanelBtn, #cancelDetailsBtn').on('click', () => $('#allowanceDeductionPanel').removeClass('open'));

    $('#saveDetailsBtn').on('click', function() {
        const form = $('#detailsForm');
        const formData = new FormData(form[0]);
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = (key === 'id') ? value : parseNumber(value);
        }

        $.ajax({
            url: `/employees/${data.id}/update-details`,
            method: 'POST', // Using POST to avoid issues with PUT and FormData
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                showAlert('success', 'Employee details updated successfully!');
                $('#allowanceDeductionPanel').removeClass('open');
                loadEmployees($('#nameSearch').val()); // Refresh grid
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMsg = 'Failed to update details.';
                if (errors) {
                    errorMsg = Object.values(errors).flat().join('<br>');
                }
                showAlert('danger', errorMsg);
            }
        });
    });

    // --- Send SMS Modal Logic ---
    let smsSelectedEmployees = [];

    $('#sendSmsBtn').on('click', function() {
        const selectedIndexes = $('#employeeGrid').jqxGrid('getselectedrowindexes');
        if (selectedIndexes.length === 0) {
            showAlert('warning', 'Please select at least one employee to send SMS.');
            return;
        }

        smsSelectedEmployees = selectedIndexes.map(index => $('#employeeGrid').jqxGrid('getrowdata', index));
        
        populateRecipientTable(smsSelectedEmployees);
        $('#smsSelectedCount').text(smsSelectedEmployees.length);

        if (smsSelectedEmployees.length > 0) {
            $('#smsRecipientList tr').first().addClass('selected');
            updateSmsPreview(smsSelectedEmployees[0]);
        }

        new bootstrap.Modal(document.getElementById('smsPreviewModal')).show();
    });

    function populateRecipientTable(employees) {
        const recipientList = $('#smsRecipientList');
        recipientList.empty();
        employees.forEach(emp => {
            const row = $(`
                <tr data-id="${emp.id}">
                    <td>${emp.employee_id}</td>
                    <td>${emp.department || 'Seoul, sec'}</td>
                    <td>${emp.name}</td>
                    <td>${emp.contact_number || '010-5555-2222'}</td>
                </tr>
            `);
            recipientList.append(row);
        });
    }

    $('#smsRecipientSearch').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        const filteredEmployees = smsSelectedEmployees.filter(emp => 
            emp.name.toLowerCase().includes(searchTerm) ||
            emp.employee_id.toString().includes(searchTerm) ||
            (emp.contact_number || '').includes(searchTerm)
        );
        populateRecipientTable(filteredEmployees);
        if (filteredEmployees.length > 0) {
             $('#smsRecipientList tr').first().addClass('selected');
            updateSmsPreview(filteredEmployees[0]);
        }
    });

    $(document).on('click', '#smsRecipientList tr', function() {
        $('#smsRecipientList tr').removeClass('selected');
        $(this).addClass('selected');
        const employeeId = $(this).data('id');
        const employeeData = smsSelectedEmployees.find(emp => emp.id == employeeId);
        updateSmsPreview(employeeData);
    });

    function updateSmsPreview(employee) {
        if (!employee) {
            $('#smsRecipientName').text('받는 사람 >');
            $('#smsPreviewContent').html(''); // Clear preview if no employee
            return;
        }

        // Update recipient name in the header
        $('#smsRecipientName').text(`${employee.name} >`);

        const f = (n) => n ? n.toLocaleString() : '0';

        // Get admin message from the new textarea
        const adminMessage = $('#adminMessageInput').val();

        const blackTextContent = 
            `<strong>[주]피에스엠씨] 급여 명세서 발송 2025년의 3월 급여가 지급되었습니다.</strong><br>` +
            `귀하의 노고에 감사드립니다.<br><br>` +
            `급여명세서 링크 확인하기<br>` +
            `www.google.com/jaden221`;

        const blueTextContent = 
            `[지급일] 2025년 04월 10일<br><br>` +
            `[사원코드] ${employee.employee_id}<br>` +
            `[사원명] ${employee.name} 님 귀하<br><br>` +
            `<strong>[2025년 3월 급여 명세서]</strong><br>` +
            `&nbsp;• 기본급 ${f(employee.base_salary)}<br>` +
            `&nbsp;• 선임(자격수당) 400,000<br>` +
            `&nbsp;• 연차수당 154,000<br>` +
            `&nbsp;▶ 지급액계 ${f(employee.total_pay)}<br><br>` +
            `&nbsp;• 건강보험 ${f(133500)}<br>` +
            `&nbsp;• 장기요양보험료 ${f(17280)}<br>` +
            `&nbsp;• 소득세 ${f(178920)}<br>` +
            `&nbsp;• 지방소득세 ${f(17890)}<br>` +
            `&nbsp;▶ 공제액계 ${f(employee.total_deduction)}`;

        const previewHtml = `
            <div class="sms-timestamp">오늘 오후</div>
            <div class="sms-bubble">
                <div>${blackTextContent}</div>
                <div style="color: #007bff; margin-top: 1rem;">${blueTextContent}</div>
                ${adminMessage ? `<br><div style="color: #000;">${adminMessage.replace(/\n/g, '<br>')}</div>` : ''}
            </div>
        `;
        
        $('#smsPreviewContent').html(previewHtml);
    }

    // Add event listener for the admin message input to update preview in real-time
    $('#adminMessageInput').on('input', function() {
        const selectedRow = $('#smsRecipientList tr.selected');
        if (selectedRow.length) {
            const employeeId = selectedRow.data('id');
            const employeeData = smsSelectedEmployees.find(emp => emp.id == employeeId);
            updateSmsPreview(employeeData);
        }
    });

    $('#importExcelBtn').on('click', () => $('#fileInput').click());

    function showAlert(type, message) {
        const alertDiv = $(`
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        $('body').append(alertDiv);
        setTimeout(() => { alertDiv.alert('close'); }, 5000);
    }
});
</script>
@endsection
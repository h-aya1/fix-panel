@extends('layouts.app')

@section('title', __('payroll.processing.title_main_suffix'))

@php
    // Use data from controller instead of hardcoded values
    $image_frame_label = "Frame 3";
    $image_page_title = $currentPayrollMonth . " " . __('payroll.processing.title_main_suffix');
    $image_total_employees_text = __('payroll.processing.total_employees_display_short', ['count' => $totalPayrollEmployees]);
    $image_sms_sent_employees_text = __('payroll.processing.sms_sent_employees_display', ['count' => $smsSentCount ?? $totalPayrollEmployees]);
    $image_excel_upload_prompt = __('payroll.processing.excel_upload_prompt_short');
    $image_filter_site_button = "SETEC";
    $image_filter_month_button = $currentPayrollMonth;
    $image_selected_count_text = __('payroll.actions.selected_count_display_short', ['count' => $totalPayrollEmployees]);
    $image_send_sms_button_text = __('payroll.actions.send_sms_selected_short');
    $image_search_label = __('app.search_short');

    // All payroll data is now passed from the controller
    $isSelectAllChecked = true;
    $jsonEncodedAttendanceData = htmlspecialchars(json_encode($attendanceLeaveDataExample), ENT_QUOTES, 'UTF-8');
@endphp

@section('page-style')
  <link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.base.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.bootstrap.css') }}" type="text/css" />
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 0.9rem; background-color: #f4f5f7; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e0e0e0; }
    .header-main-title { font-size: 1.75rem; font-weight: 600; color: #333; }
    .header-sub-info { display: flex; gap: 1.5rem; font-size: 0.875rem; color: #555; margin-top: 0.3rem; }
    .excel-upload-area { border: 2px dashed #ced4da; padding: 30px 20px; text-align: center; margin-bottom: 1.5rem; background-color: #fff; border-radius: 0.3rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .upload-icon { width: 40px; height: 40px; background-color: #e9ecef; margin: 0 auto 15px auto; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #495057; }
    .upload-icon::before { content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='currentColor' class='bi bi-cloud-arrow-up-fill' viewBox='0 0 16 16'%3E%3Cpath d='M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z'/%3E%3C/svg%3E"); }
    .excel-upload-area p { margin-bottom: 1rem; color: #495057; font-size: 0.95rem; }
    .excel-upload-area .btn { font-size: 0.8rem; padding: 0.4rem 0.8rem; }
    .actions-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem; }
    .actions-bar .btn { font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 0.25rem; }
    .actions-bar .actions-bar-left .btn-action-filter { border: 1px solid #ced4da; color: #495057; background-color: #fff; }
    .actions-bar .actions-bar-left .btn-action-filter:hover { background-color: #e9ecef; }
    .actions-bar .actions-bar-left .btn-action-filter.active { background-color: #0d6efd; color: white; border-color: #0d6efd; }
    .actions-bar .btn-send-sms-custom { background-color: #198754; border-color: #198754; color: white; }
    .actions-bar .btn-send-sms-custom:hover { background-color: #157347; border-color: #146c43; }
    .selected-count-display { font-size: 0.875rem; color: #495057; margin-right: 0.75rem; }
    .search-action-group { display: flex; align-items: center; }
    .search-action-group .input-group-text { font-size: 0.8rem; background-color: #e9ecef; border-color: #ced4da; border-right: none; border-radius: 0.25rem 0 0 0.25rem; padding: 0.4rem 0.6rem; }
    .search-action-group .form-control-sm { font-size: 0.8rem; border-color: #ced4da; max-width: 200px; border-left: none; border-radius: 0 0.25rem 0.25rem 0; padding: 0.4rem 0.6rem; }
    .search-action-group .form-control-sm:focus { box-shadow: 0 0 0 0.2rem rgba(13,110,253,.25); border-color: #86b7fe; }
    #payrollJqxGrid { width: 100%; height: 600px; border: 1px solid #dee2e6; border-radius: 0.25rem; }
    .jqx-grid-cell-selected { background-color: #cfe2ff !important; }
    .jqx-grid-cell-hover { background-color: #e7f1ff !important; }
    .jqx-grid-cell.jqx-grid-cell-pinned { background-color: #f8f9fa !important; font-weight: 500;}
    .clickable-cell { cursor: pointer; color: #0d6efd; }
    .clickable-cell:hover { text-decoration: underline; }
    .status-badge-cell { display: flex; align-items: center; justify-content: center; height: 100%; width: 100%; padding: 0 3px; }
    .status-badge { padding: 0.35em 0.65em; font-size: 0.75em; font-weight: 600; color: #fff; text-align: center; white-space: nowrap; line-height: 1.2; border-radius: 0.25rem; display: inline-block; min-width: 50px; }
    .sms-status-icon { font-weight: bold; font-size: 1.1em; }
    .sms-status-icon.sent { color: #198754; } .sms-status-icon.failed { color: #dc3545; } .sms-status-icon.pending { color: #6c757d; font-size: 1.2em; line-height: 1; }
    .offcanvas-header { border-bottom: 1px solid #e0e0e0; padding: 1rem 1.25rem; }
    .offcanvas-title { font-size: 1.15rem; font-weight: 600; }
    .offcanvas-body { padding: 1.25rem; }
    .offcanvas-footer { border-top: 1px solid #e0e0e0; padding: 1rem 1.25rem; background-color: #f8f9fa; }
    .offcanvas-header-custom { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid #e0e0e0; }
    .offcanvas-title-main { font-size: 1.1rem; font-weight: 600; }
    .offcanvas-title-sub { font-size: 0.85rem; color: #6c757d; margin-top: 0.2rem; }
    .work-days-input-group { display: flex; align-items: center; margin: 1rem 0; padding-bottom: 1rem; border-bottom: 1px solid #eee; }
    .work-days-input-group label { margin-right: 0.5rem; font-size: 0.875rem; white-space: nowrap; }
    .work-days-input-group input { max-width: 70px; text-align: right; font-size: 0.875rem; padding: 0.3rem 0.5rem; }
    .attendance-table-container { margin-bottom: 1rem; }
    .attendance-table th, .attendance-table td { font-size: 0.8rem; padding: 0.4rem; text-align: center; vertical-align: middle; white-space: normal; }
    .attendance-table thead th { background-color: #f8f9fa; font-weight: 600;}
    .offcanvas-footer-custom { padding: 1rem 1.25rem; border-top: 1px solid #e0e0e0; background-color: #f8f9fa; }
    #offcanvasPayrollDetails .btn-print-payroll { background-color: #6c757d; border-color: #6c757d; color:white; width: 100%; margin-top: 0.75rem; font-size: 0.9rem; padding: 0.5rem;}
    #offcanvasPayrollDetails .btn-print-payroll:hover { background-color: #5a6268; border-color: #545b62; }
    #offcanvasPayrollDetails .btn-modify-payroll { width: 100%; font-size: 0.9rem; padding: 0.5rem;}
    .base-salary-item { display: flex; justify-content: space-between; align-items: center; background-color: #f8f9fa; padding: 0.75rem 1rem; border: 1px solid #dee2e6; border-radius: 0.25rem; margin-bottom: 1.5rem; }
    .base-salary-item .label { font-weight: 600; color: #495057; }
    .base-salary-item .amount { font-weight: bold; color: #343a40; font-size: 1rem; }
    .payroll-items-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .payroll-detail-section .section-title { font-size: 1rem; font-weight: 600; color: #343a40; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.5rem; border-bottom: 1px solid #eee; }
    .payroll-detail-section .section-title .total-amount { font-size: 1rem; font-weight: bold; }
    .payroll-items-table td { padding: 0.5rem 0.6rem; font-size: 0.85rem; border: 1px solid #f0f0f0; }
    .payroll-items-table td:first-child { background-color: #f8f9fa; color: #495057; width: 60%; }
    .payroll-items-table td:last-child { text-align: right; font-weight: 500; }
    #offcanvasSmsPreview .offcanvas-header, #offcanvasPayrollDetails .offcanvas-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e0e0e0; align-items: center; }
    #offcanvasSmsPreview .btn-close-sms-custom, #offcanvasPayrollDetails .btn-close-custom { font-size: 1.5rem; font-weight: bold; color: #000; opacity: 0.5; background: transparent; border: none; padding: 0; line-height: 1; }
    #offcanvasSmsPreview .btn-close-sms-custom:hover, #offcanvasPayrollDetails .btn-close-custom:hover { opacity: 0.75; }
    .offcanvas-body .form-control, .offcanvas-body .form-select { font-size: 0.875rem; padding: 0.4rem 0.75rem; }
    .offcanvas-body .form-label { font-size: 0.875rem; margin-bottom: 0.3rem; font-weight: 500; }
    .offcanvas-body hr { margin: 1.25rem 0; }
    .offcanvas-body h6.text-muted { font-size: 0.9rem; color: #6c757d !important; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; }
    #offcanvasSmsPreview .offcanvas-header { /* Keep this if it's good */
    padding: 1rem 1.25rem; border-bottom: 1px solid #e0e0e0; align-items: center;
}
#offcanvasSmsPreview .btn-close-sms-custom { /* Keep this */
    font-size: 1.5rem; font-weight: bold; color: #000; opacity: 0.5;
    background: transparent; border: none; padding: 0; line-height: 1;
}
#offcanvasSmsPreview .btn-close-sms-custom:hover { opacity: 0.75; }
#offcanvasSmsPreview .offcanvas-title { font-size: 1.15rem; font-weight: 600; }

#offcanvasSmsPreview .offcanvas-body { display: flex; padding: 0; height: calc(100% - 57px - 73px); }

/* Left Panel (Employee List) - Keep styles mostly as before */
.sms-offcanvas-left-panel {
    width: 38%; /* Slightly adjust if needed */
    padding: 1rem; /* Consistent padding */
    border-right: 1px solid #e0e0e0;
    display: flex; flex-direction: column; background-color: #fff; /* White background */
}
.sms-offcanvas-left-panel .selected-info { font-size: 0.9rem; font-weight: 600; margin-bottom: 0.75rem; color: #333; }
.sms-offcanvas-left-panel .search-employee-sms input {
    font-size: 0.8rem; margin-bottom: 0.75rem;
    border-radius: 0.25rem; border: 1px solid #ced4da;
}
.employee-sms-list-container {
    flex-grow: 1; overflow-y: auto; border: 1px solid #e9ecef; border-radius: 0.25rem;
}
.employee-sms-list-table { width: 100%; font-size: 0.78rem; /* Slightly larger font */ }
.employee-sms-list-table th, .employee-sms-list-table td {
    padding: 0.5rem 0.6rem; /* More padding */
    text-align: left; border-bottom: 1px solid #f0f0f0; white-space: nowrap;
}
.employee-sms-list-table thead th {
    background-color: #f8f9fa; font-weight: 600; font-size:0.75rem; color: #495057;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.employee-sms-list-table tbody tr { cursor: pointer; }
.employee-sms-list-table tbody tr:hover { background-color: #e9f5ff; } /* Lighter blue hover */
.employee-sms-list-table tbody tr.active-sms-employee {
    background-color: #0d6efd; color: white; font-weight: 500;
}
.employee-sms-list-table tbody tr.active-sms-employee td { color: white; }


/* Right Panel (Phone Preview) - Major Styling Changes */
.sms-offcanvas-right-panel {
    width: 62%; /* Adjust if needed */
    padding: 1rem; /* Overall padding for the right panel */
    display: flex; flex-direction: column;
    background-color: #e5e5ea; /* iOS-like background for message area */
}

.phone-preview-outer { /* New wrapper for centering phone and adding space for template */
    flex-grow: 1; display: flex; flex-direction: column; align-items: center;
    justify-content: flex-start; /* Align phone to top */
    padding-top: 1rem; /* Space above phone */
}

.phone-preview-container {
    width: 320px; /* More standard phone width */
    height: 568px; /* iPhone 5-ish height */
    background-color: #f4f5f7; /* The "inside" background of the phone messages app */
    border-radius: 36px; /* More pronounced rounding */
    box-shadow: 0 8px 20px rgba(0,0,0,0.1), 0 0 0 1px rgba(0,0,0,0.05); /* Softer, layered shadow */
    padding: 10px 0px 0px 0px; /* No padding inside, notch and bars will have their own */
    display: flex; flex-direction: column;
    overflow: hidden; position: relative;
    border: 6px solid black; /* Phone bezel */
}

.phone-preview-top-bar { /* Status bar area */
    height: 20px; /* Approximate height of iOS status bar */
    display: flex; justify-content: space-between; align-items: center;
    padding: 0 12px; /* Padding for time and icons */
    font-size: 0.7rem; color: #000; /* Status bar text color */
    position: absolute; top: 8px; left: 0; right: 0; z-index: 15; /* On top of notch slightly */
}
.phone-preview-top-bar .time { font-weight: 600; }
.phone-preview-top-bar .status-icons i { margin-left: 4px; } /* For FontAwesome or similar icons */

.phone-preview-notch {
    width: 120px; height: 28px; background-color: #000; /* Black notch */
    border-radius: 0 0 12px 12px; /* Rounded bottom */
    position: absolute; top: 6px; left: 50%; transform: translateX(-50%);
    z-index: 10; /* Behind status bar text potentially */
}

.phone-preview-contact-header { /* The bar with contact name and back arrow */
    padding: 8px 12px;
    background-color: #f7f7f7; /* iOS header bar color */
    border-bottom: 0.5px solid #c7c7cc; /* iOS thin separator */
    display: flex; align-items: center;
    margin-top: 25px; /* To clear notch and status bar */
    text-align: center; /* Center contact name */
    position: relative; /* For back button positioning */
}
.phone-preview-contact-header .back-arrow {
    font-size: 1.1rem; color: #007aff; /* iOS blue */
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    font-weight: 400;
}
.phone-preview-contact-header .contact-name-details { flex-grow: 1; }
.phone-preview-contact-header .contact-name { font-size: 0.95rem; font-weight: 600; color: #000; }
.phone-preview-contact-header .contact-subtext { font-size: 0.7rem; color: #8e8e93; } /* e.g., "mobile" */


.phone-preview-messages {
    flex-grow: 1; overflow-y: auto;
    padding: 10px; /* Padding for message bubbles */
    background-color: #e5e5ea; /* iOS message background */
    display: flex; flex-direction: column;
}
.phone-preview-messages .sms-timestamp {
    text-align: center; color: #8e8e93; /* iOS timestamp color */
    font-size: 0.7rem; margin: 10px 0; text-transform: uppercase;
}
.sms-bubble-wrapper { /* New wrapper for alignment */
    display: flex; margin-bottom: 2px; /* Small gap between consecutive bubbles from same sender */
    width: 100%;
}
.sms-bubble-wrapper.received { justify-content: flex-start; } /* Align left */
.sms-bubble-wrapper.sent { justify-content: flex-end; } /* Align right */

.sms-bubble {
    padding: 8px 14px;
    border-radius: 18px; /* iOS bubble radius */
    max-width: 75%; /* Max width for bubbles */
    word-wrap: break-word;
    line-height: 1.4;
    font-size: 0.9rem; /* Standard iOS message font size */
    box-shadow: 0 1px 0.5px rgba(0,0,0,0.13); /* Subtle shadow */
}
.sms-bubble.received { /* Grey bubble from other party */
    background-color: #fff; /* iOS received bubble */
    color: #000;
    border-bottom-left-radius: 4px; /* Tail effect */
}
.sms-bubble.sent { /* Blue bubble from user (if we were to show user messages) */
    background-color: #007aff; /* iOS blue */
    color: white;
    border-bottom-right-radius: 4px; /* Tail effect */
}
.sms-bubble strong { font-weight: 600; } /* For company name */
.sms-bubble a { color: #007aff; text-decoration: none; } /* iOS link blue */
.sms-bubble a:hover { text-decoration: underline; }

/* Styling for payroll details within the bubble */
.payroll-info-block { margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(0,0,0,0.05); }
.payroll-info-block .sms-item {
    display: flex; justify-content: space-between;
    font-size: 0.8rem; /* Slightly smaller for details */
    margin-bottom: 3px;
    line-height: 1.3;
}
.payroll-info-block .sms-item-label { color: #333; }
.payroll-info-block .sms-item-value { font-weight: 500; color: #000; }
.payroll-info-block .sms-total {
    display: flex; justify-content: space-between;
    font-weight: 600; margin-top: 6px;
    font-size: 0.85rem;
    color: #000; /* Totals often black in iOS messages unless links */
}


/* Message Template Area and Info below phone */
.sms-controls-area {
    margin-top: 1.5rem; /* Space below phone preview */
    width: 100%;
    max-width: 450px; /* Limit width of controls for better layout */
    align-self: center; /* Center controls if phone is also centered */
}
.sms-message-template-area { margin-bottom: 1rem; }
.sms-message-template-area label {
    font-size: 0.8rem; font-weight: 500; margin-bottom: 0.3rem; display: block; color: #495057;
}
.sms-message-template-area textarea {
    font-size: 0.8rem; min-height: 70px;
    border-radius:0.25rem; border: 1px solid #ced4da; width: 100%; padding: 0.5rem;
}
.sms-info-group {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 0.8rem; background-color: #f8f9fa; padding: 0.6rem 0.9rem;
    border-radius: 0.25rem; margin-bottom: 0.5rem; border: 1px solid #e9ecef;
}
.sms-info-group .label { color: #495057; }
.sms-info-group .value { font-weight: 600; color: #343a40; }

#offcanvasSmsPreview .offcanvas-footer { /* Keep this */
    padding: 0.8rem 1.25rem; border-top: 1px solid #e0e0e0; background-color: #f8f9fa;
}
#offcanvasSmsPreview .btn-resend-sms { /* Keep this */
    background-color: #0d6efd; border-color: #0d6efd; /* Using primary blue */
    color: white; width: 100%; font-size: 0.9rem; padding: 0.5rem;
}
#offcanvasSmsPreview .btn-resend-sms:hover { background-color: #0b5ed7; border-color: #0a58ca; }
  </style>
@endsection

@section('page-script')
  {{-- jqxWidget script includes --}}
  <script src="{{ asset('jqwidgets/jqxcore.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxdata.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxbuttons.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxscrollbar.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxmenu.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxcheckbox.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxlistbox.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxdropdownlist.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxgrid.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxgrid.pager.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxgrid.sort.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxgrid.filter.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxgrid.selection.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxgrid.edit.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxgrid.columnsresize.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxgrid.columnsreorder.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxcalendar.js') }}"></script>
  <script src="{{ asset('jqwidgets/jqxdatetimeinput.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>

  <script>
    // Global Helper Functions
    function formatNumber(num, defaultValue = '-') {
        if (num === null || num === undefined || num === '' || isNaN(Number(num))) return defaultValue;
        try { return Number(num).toLocaleString('ko-KR'); } catch (e) { return num; }
    }

    // Make currentPayrollMonthJS global for access in functions defined outside $(document).ready
    var currentPayrollMonthJS = "{{ $currentPayrollMonth }}";

    function showPayrollDetails(rowUID) {
        const grid = $("#payrollJqxGrid");
        const rowData = grid.jqxGrid('getrowdatabyid', rowUID);
        if (!rowData) { console.error('Payroll Details: Row data not found for UID:', rowUID); return; }

        $('#offcanvasPayrollDetailsTitle').text(`${rowData.id} ${rowData.name} ${currentPayrollMonthJS} {{ __('payroll.offcanvas_details.title_suffix') }}`);
        $('#payrollDetailBaseSalaryLabel').text(`{{ __('payroll.offcanvas_details.base_salary_label') }}`);
        $('#payrollDetailBaseSalaryAmount').text(rowData.base_salary_str); // Display string version

        // Use numeric versions for calculations/totals if available, otherwise parse strings
        let numericTotalAllowances = rowData.numeric_total_allowances !== undefined ? rowData.numeric_total_allowances : parseFloat(String(rowData.allowances_str || '0').replace(/,/g, ''));
        let numericTotalDeductions = rowData.numeric_total_deductions !== undefined ? rowData.numeric_total_deductions : parseFloat(String(rowData.deductions_str || '0').replace(/,/g, ''));

        $('#payrollDetailAllowancesTotal').text(formatNumber(numericTotalAllowances));
        const allowancesTableBody = $('#payrollDetailAllowancesTableBody');
        allowancesTableBody.empty();
        if (rowData.allowance_items && rowData.allowance_items.length > 0) {
            rowData.allowance_items.forEach(item => { allowancesTableBody.append(`<tr><td>${item.label_translation}</td><td class="amount">${formatNumber(item.value)}</td></tr>`); });
        } else { allowancesTableBody.append(`<tr><td colspan="2" class="text-center text-muted small">- No allowance data -</td></tr>`);}

        $('#payrollDetailDeductionsTotal').text(formatNumber(numericTotalDeductions));
        const deductionsTableBody = $('#payrollDetailDeductionsTableBody');
        deductionsTableBody.empty();
        if (rowData.deduction_items && rowData.deduction_items.length > 0) {
            rowData.deduction_items.forEach(item => { deductionsTableBody.append(`<tr><td>${item.label_translation}</td><td class="amount">${formatNumber(item.value)}</td></tr>`); });
        } else { deductionsTableBody.append(`<tr><td colspan="2" class="text-center text-muted small">- No deduction data -</td></tr>`);}

        let printRoute = "{{ route('payrolls.print', ['id' => ':id_placeholder']) }}"; // Ensure this route exists
        $('#offcanvasPayrollDetails .btn-print-payroll').attr('href', printRoute.replace(':id_placeholder', rowData.id)); // Assuming rowData.id is the employee ID for the payslip

        var offcanvasEl = document.getElementById('offcanvasPayrollDetails');
        var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
        offcanvasInstance.show();
    }

    function showAttendanceDetails(rowUID) {
        const grid = $("#payrollJqxGrid");
        const rowData = grid.jqxGrid('getrowdatabyid', rowUID);
        if (!rowData) { console.error('Attendance Details: Row data not found for UID:', rowUID); return; }

        $('#offcanvasAttendanceLeaveLabelEmployee').text(`${rowData.id} ${rowData.name}`);
        // Update offcanvas title with the current payroll month
        $('#offcanvasAttendanceLeaveLabelMonth').text(`${currentPayrollMonthJS} {{ __('offcanvas.attendance.title_suffix') }}`);
        $('#offcanvasWorkDaysInput').val(rowData.work_days);

        const attendanceTableBody = $('#attendanceLeaveTableBody');
        attendanceTableBody.empty();
        const exampleDataString = document.getElementById('offcanvasAttendanceLeave').getAttribute('data-example-attendance');
        let exampleData = [];
        if (exampleDataString) { try { exampleData = JSON.parse($('<textarea />').html(exampleDataString).text()); } catch (e) { console.error("Error parsing attendance data:", e); }}
        if (exampleData && exampleData.length > 0) {
            exampleData.forEach(item => { attendanceTableBody.append(`<tr><td>${item.type || '-'}</td><td>${item.date || '-'}</td><td>${item.period || '-'}</td><td>${item.paid || '-'}</td><td>${item.memo || '-'}</td></tr>`);});
        } else {
            attendanceTableBody.append(`<tr><td colspan="5" class="text-center text-muted small">{{ __('offcanvas.attendance.no_data') }}</td></tr>`);
        }

        var offcanvasEl = document.getElementById('offcanvasAttendanceLeave');
        var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
        offcanvasInstance.show();
    }

    $(document).ready(function () {
        const initialPayrollData = @json($payrollEntriesData);
        // If join_date_str exists, convert it to Date objects for jqxGrid date column type
        initialPayrollData.forEach(emp => {
            if (emp.join_date_str) { // Assuming you might add this field later for CSV import
                emp.join_date = new Date(emp.join_date_str);
            }
        });

        var source = {
            localdata: initialPayrollData,
            datatype: "array",
            id: 'uid', // This field must exist in your data and be unique
            datafields: [
                { name: 'uid', type: 'string' },
                { name: 'is_checked', type: 'bool'}, // For initial checkbox state if bound
                { name: 'id', type: 'string' },
                { name: 'department', type: 'string' },
                { name: 'position', type: 'string' },
                { name: 'name', type: 'string' },
                { name: 'work_days', type: 'number' },
                { name: 'base_salary_str', type: 'string' }, // For display
                { name: 'allowances_str', type: 'string' }, // For display
                { name: 'gross_pay_str', type: 'string' },   // For display
                { name: 'deductions_str', type: 'string' }, // For display
                { name: 'net_pay_str', type: 'string' },     // For display
                { name: 'remarks', type: 'string' },
                { name: 'sms_sent_status', type: 'string' },
                { name: 'phone_number', type: 'string'}, // For SMS offcanvas
                // Numeric fields for calculations or offcanvas if not parsing strings there
                { name: 'numeric_base_salary', type: 'number' },
                { name: 'numeric_total_allowances', type: 'number' },
                { name: 'numeric_total_deductions', type: 'number' },
                { name: 'numeric_net_pay', type: 'number' },
                // Complex data for offcanvases
                { name: 'allowance_items', type: 'array' },
                { name: 'deduction_items', type: 'array' },
                { name: 'sms_details', type: 'object' }
            ]
        };
        var dataAdapter = new $.jqx.dataAdapter(source);

        // Cell Renderers
        var cellsrenderer_text_center = function (row, columnfield, value) { return '<div style="text-align: center; margin: 5px 3px; white-space: normal; word-wrap: break-word;">' + (value || '') + '</div>'; };
        var cellsrenderer_text_right_bold = function (row, columnfield, value) { return '<div style="text-align: right; font-weight: 500; margin: 5px 3px;">' + (value || '') + '</div>'; };
        var cellsrenderer_employee_id = function (row, column, value, defaulthtml, columnproperties, rowdata) { return `<div class="clickable-cell" style="text-align: center; margin: 5px 3px; background-color: #f0f8ff;" onclick="showAttendanceDetails('${rowdata.uid}')">${value}</div>`; };
        var cellsrenderer_employee_name = function (row, column, value, defaulthtml, columnproperties, rowdata) { return `<div class="clickable-cell" style="text-align: center; margin: 5px 3px;" onclick="showAttendanceDetails('${rowdata.uid}')">${value}</div>`; };
        var cellsrenderer_net_pay = function (row, column, value, defaulthtml, columnproperties, rowdata) { return `<div class="clickable-cell" style="text-align: right; font-weight: bold; margin: 5px 3px;" onclick="showPayrollDetails('${rowdata.uid}')">${value}</div>`; };
        var cellsrenderer_sms_status = function (row, column, value) {
            let iconClass = 'sms-status-icon pending'; let iconChar = '-';
            if (value === 'sent') { iconClass = 'sms-status-icon sent'; iconChar = '✔'; }
            else if (value === 'failed') { iconClass = 'sms-status-icon failed'; iconChar = '✘'; }
            return `<div style="text-align:center; margin-top:5px;"><span class="${iconClass}">${iconChar}</span></div>`;
        };

        // Grid Initialization
        $("#payrollJqxGrid").jqxGrid({
            width: '100%',
            source: dataAdapter,
            theme: 'bootstrap', // Using Bootstrap theme
            pageable: true,
            pagesize: initialPayrollData.length > 50 ? 50 : (initialPayrollData.length > 20 ? 20 : (initialPayrollData.length > 0 ? Math.max(initialPayrollData.length, 10) : 10)),
            pagesizeoptions: ['10', '20', '50', '100'],
            sortable: true,
            altrows: true, // Alternate row styling
            enabletooltips: true,
            editable: false, // Grid cells not directly editable by default
            selectionmode: 'checkbox', // Enables row selection via checkboxes in the first column
            filterable: true,
            showfilterrow: true, // Display filter row beneath column headers
            columnsresize: true,
            columnsreorder: true,
            rendered: function (type) {
                if (type === "header") {
                    var grid = $("#payrollJqxGrid");
                    var checkboxColumnHeader = grid.find(".jqx-grid-column-header:first");
                    if (checkboxColumnHeader.find('#jqxGridSelectAllCheckboxExternal').length === 0) {
                        checkboxColumnHeader.html(''); // Clear any existing content
                        var selectAllContainer = $("<div style='width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;'></div>");
                        var selectAllInput = $("<input type='checkbox' id='jqxGridSelectAllCheckboxExternal' class='form-check-input' style='margin: 0; transform: scale(0.9);' />");
                        selectAllContainer.append(selectAllInput);
                        checkboxColumnHeader.append(selectAllContainer);
                        selectAllInput.on('change', function () {
                            $(this).is(':checked') ? grid.jqxGrid('selectallrows') : grid.jqxGrid('clearselection');
                        });
                    }
                }
            },
            columns: [
              { text: '', datafield: 'is_checked', columntype: 'checkbox', width: '3%', sortable: false, filterable: false, groupable: false, pinned: true, editable: true },
              { text: "{{ __('payroll.table.header.employee_id_short') }}", datafield: 'id', width: '6%', pinned: true, cellsrenderer: cellsrenderer_employee_id },
              { text: "{{ __('payroll.table.header.department') }}", datafield: 'department', width: '8%', cellsrenderer: cellsrenderer_text_center, filtertype: 'checkedlist' }, // Example filter
              { text: "{{ __('payroll.table.header.position') }}", datafield: 'position', width: '7%', cellsrenderer: cellsrenderer_text_center, filtertype: 'checkedlist' }, // Example filter
              { text: "{{ __('payroll.table.header.name') }}", datafield: 'name', width: '7%', pinned: true, cellsrenderer: cellsrenderer_employee_name },
              { text: "{{ __('payroll.table.header.work_days_short') }}", datafield: 'work_days', width: '6%', cellsalign: 'right', filtertype: 'number', cellsrenderer: cellsrenderer_text_right_bold },
              { text: "{{ __('payroll.table.header.base_salary_short') }}", datafield: 'base_salary_str', width: '9%', cellsrenderer: cellsrenderer_text_right_bold, filtertype: 'textbox' },
              { text: "{{ __('payroll.table.header.allowances_total_short') }}", datafield: 'allowances_str', width: '9%', cellsrenderer: cellsrenderer_text_right_bold, filtertype: 'textbox' },
              { text: "{{ __('payroll.table.header.gross_pay_short') }}", datafield: 'gross_pay_str', width: '9%', cellsrenderer: cellsrenderer_text_right_bold, filtertype: 'textbox' },
              { text: "{{ __('payroll.table.header.deductions_total_short') }}", datafield: 'deductions_str', width: '9%', cellsrenderer: cellsrenderer_text_right_bold, filtertype: 'textbox' },
              { text: "{{ __('payroll.table.header.net_pay_short') }}", datafield: 'net_pay_str', width: '9%', cellsrenderer: cellsrenderer_net_pay, filtertype: 'textbox' },
              { text: "{{ __('payroll.table.header.remarks') }}", datafield: 'remarks', width: '12%', cellsalign: 'left', filterable: true, cellsrenderer: cellsrenderer_text_center }, // Centered remarks
              { text: "{{ __('payroll.table.header.sms_sent_status_short') }}", datafield: 'sms_sent_status', width: '5%', cellsrenderer: cellsrenderer_sms_status, filtertype: 'checkedlist', filteritems: [{value:'sent', label:'Sent'},{value:'pending',label:'Pending'},{value:'failed',label:'Failed'}] }
            ]
        });

        // Selected Count & Select All Checkbox Logic
        const selectedTextTemplate = "{{ __('payroll.actions.selected_count_display_short', ['count' => ':count_placeholder']) }}";
        function updateSelectedCountAndSelectAll() {
            var grid = $("#payrollJqxGrid");
            var selectedrowindexes = grid.jqxGrid('getselectedrowindexes');
            $('#selectedCountDisplay').text(selectedTextTemplate.replace(':count_placeholder', selectedrowindexes.length));

            var selectAllCheckbox = $('#jqxGridSelectAllCheckboxExternal');
            if (!selectAllCheckbox.length) return;

            var datainformation = grid.jqxGrid('getdatainformation');
            var allBoundRowsCount = datainformation.rowscount;

            if (allBoundRowsCount === 0) {
                selectAllCheckbox.prop('checked', false);
                selectAllCheckbox.prop('indeterminate', false);
                return;
            }

            if (selectedrowindexes.length === allBoundRowsCount) {
                 selectAllCheckbox.prop('checked', true);
                 selectAllCheckbox.prop('indeterminate', false);
            } else if (selectedrowindexes.length > 0) {
                 selectAllCheckbox.prop('checked', false);
                 selectAllCheckbox.prop('indeterminate', true);
            } else {
                 selectAllCheckbox.prop('checked', false);
                 selectAllCheckbox.prop('indeterminate', false);
            }
        }
        $('#payrollJqxGrid').on('rowselect rowunselect bindingcomplete filter sort pagechanged pagesizechanged', updateSelectedCountAndSelectAll);
        updateSelectedCountAndSelectAll(); // Initial call

        // Global Search Input
        $('#globalTableSearchInput').on('keyup', function () {
            var searchValue = $(this).val();
            var grid = $("#payrollJqxGrid");
            grid.jqxGrid('clearfilters'); // Clear all previous filters
            if (searchValue) {
                var filtergroup = new $.jqx.filter();
                var operator = 0; // 0 for OR logic between column filters
                // Define columns to search in for payroll
                var stringColumnsToSearch = ['id', 'department', 'position', 'name', 'remarks'];
                stringColumnsToSearch.forEach(function(datafield) {
                    var filter = filtergroup.createfilter('stringfilter', searchValue, 'CONTAINS');
                    filtergroup.addfilter(operator, filter);
                    // Apply this group to each searchable column - jqxGrid ORs filters added with the same group
                    // A bit counter-intuitive, but addfilter for different datafields with the same group makes them ORed.
                    // If you want AND across columns for a single search term, it's more complex.
                    // For now, this will find rows where *any* of these columns contain the search term.
                     grid.jqxGrid('addfilter', datafield, filtergroup, false); // Add but don't apply immediately
                });
                grid.jqxGrid('applyfilters');
            }
        });

        // Filter Buttons
        $('.actions-bar-left .btn-action-filter').on('click', function() {
            var $button = $(this);
            var datafield = $button.data('datafield');
            var filtervalue = $button.data('filter-value');
            if (!datafield) { console.warn("Filter button clicked without data-datafield attribute."); return; }

            var grid = $("#payrollJqxGrid");
            grid.jqxGrid('removefilter', datafield, false); // Remove filter for this specific field without applying

            if ($button.hasClass('active')) { // If it was active, toggle it off
                $button.removeClass('active');
            } else { // If it was not active, activate it and deactivate others for the same field
                // Deactivate other buttons that might be filtering the same datafield
                $button.siblings('.btn-action-filter.active[data-datafield="'+datafield+'"]').removeClass('active');

                var filtergroup = new $.jqx.filter();
                // Assuming EQUAL for these specific filter buttons
                var filter = filtergroup.createfilter('stringfilter', filtervalue, 'EQUAL');
                filtergroup.addfilter(0, filter);
                grid.jqxGrid('addfilter', datafield, filtergroup, false); // Add filter without applying yet
                $button.addClass('active');
            }
            grid.jqxGrid('applyfilters'); // Apply all accumulated filters
        });

        // Function to refresh the payroll grid data
        function refreshPayrollGrid() {
            $.ajax({
                url: '{{ route("payrolls.index") }}',
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    // Update the grid data source
                    const source = {
                        datatype: "json",
                        datafields: [
                            { name: 'uid' }, { name: 'id' }, { name: 'name' }, { name: 'department' }, { name: 'position' },
                            { name: 'work_days' }, { name: 'base_salary_str' }, { name: 'allowances_str' }, { name: 'gross_pay_str' },
                            { name: 'deductions_str' }, { name: 'net_pay_str' }, { name: 'remarks' }, { name: 'sms_sent_status' },
                            { name: 'is_checked' }, { name: 'numeric_base_salary' }, { name: 'numeric_total_allowances' },
                            { name: 'numeric_total_deductions' }, { name: 'numeric_net_pay' }, { name: 'allowance_items' },
                            { name: 'deduction_items' }, { name: 'sms_details' }
                        ],
                        localdata: response
                    };
                    const dataAdapter = new $.jqx.dataAdapter(source);
                    $('#payrollJqxGrid').jqxGrid({ source: dataAdapter });
                    
                    // Update empty state
                    setTimeout(toggleEmptyState, 200);
                    
                    // Update statistics if elements exist
                    if (response.length > 0) {
                        const smsSentCount = response.filter(item => item.sms_sent_status === 'sent').length;
                        $('.header-sub-info span:nth-child(1)').text('{{ __("payroll.processing.total_employees_display_short", ["count" => ""]) }}'.replace(':count', response.length));
                        $('.header-sub-info span:nth-child(2)').text('{{ __("payroll.processing.sms_sent_employees_display", ["count" => ""]) }}'.replace(':count', smsSentCount));
                    }
                },
                error: function(xhr) {
                    console.error('Failed to refresh payroll data:', xhr);
                }
            });
        }

        // Function to toggle empty state visibility
        function toggleEmptyState() {
            const grid = $("#payrollJqxGrid");
            const emptyState = $("#emptyPayrollState");
            const rowsCount = grid.jqxGrid('getdatainformation').rowscount;
            
            if (rowsCount === 0) {
                grid.hide();
                emptyState.show();
            } else {
                grid.show();
                emptyState.hide();
            }
        }

        // Initial empty state check
        toggleEmptyState();

        // Update empty state when data changes
        $('#payrollJqxGrid').on('bindingcomplete', function() {
            setTimeout(toggleEmptyState, 100); // Small delay to ensure grid is fully rendered
        });

        // Excel/CSV Upload and Preview Logic
        $('#excelFile').on('change', function(event) { /* ... same logic as before ... */ });
        function parseAndDisplayCsv(file) { /* ... same logic as before ... */ }
        function displayCsvData(data, headers) { /* ... same logic, ensure translation key is correct ... */
            const previewTableHead = $('#csvPreviewTable thead');
            const previewTableBody = $('#csvPreviewTable tbody');
            previewTableHead.empty(); previewTableBody.empty();

            if (!data || data.length === 0) {
                let colspan = headers ? headers.length : 1;
                previewTableBody.append(`<tr><td colspan="${colspan}" class="text-muted small">{{ __('employee.management.empty_file_preview') }}</td></tr>`);
                $('#csvPreviewArea').show(); return;
            }
            let headerRowHTML = '<tr>';
            const effectiveHeaders = headers && headers.length > 0 ? headers : (data[0] ? Object.keys(data[0]) : []);
            effectiveHeaders.forEach(header => { headerRowHTML += `<th>${header}</th>`; });
            headerRowHTML += '</tr>';
            previewTableHead.append(headerRowHTML);

            const previewLimit = 10;
            data.slice(0, previewLimit).forEach(row => {
                let bodyRowHTML = '<tr>';
                effectiveHeaders.forEach(header => { bodyRowHTML += `<td>${row[header] != null ? row[header] : ''}</td>`; });
                bodyRowHTML += '</tr>';
                previewTableBody.append(bodyRowHTML);
            });
            if (data.length > previewLimit) {
                let colspan = effectiveHeaders.length > 0 ? effectiveHeaders.length : 1;
                let remainingRows = data.length - previewLimit;
                let moreRowsText = "{{ __('employee.management.and_more_rows_placeholder') }}".replace(':count', remainingRows);
                previewTableBody.append(`<tr><td colspan="${colspan}" class="text-muted text-center small">... ${moreRowsText} ...</td></tr>`);
            }
            $('#csvPreviewArea').data('parsedData', data);
            $('#csvPreviewArea').show();
        }
        $('#cancelCsvPreviewBtn').on('click', function() { /* ... same ... */ });
        $('#importCsvDataBtn').on('click', function() { /* ... same, ensure robust PAYROLL mapping ... */
            const importedData = $('#csvPreviewArea').data('parsedData');
            if (!importedData || importedData.length === 0) { alert("{{ __('employee.management.no_data_to_import') }}"); return; }

            const mappedPayrollData = importedData.map((row, index) => {
                let newUid = 'imported_payroll_' + Date.now() + '_' + index; // Ensure UID is unique
                return {
                    uid: newUid,
                    is_checked: false, // Default for new rows
                    id: String(row['사원번호'] || row['EmpID'] || newUid), // Ensure ID is string
                    department: String(row['부서'] || row['Department'] || ''),
                    position: String(row['직책'] || row['Position'] || ''),
                    name: String(row['이름'] || row['Name'] || 'N/A'),
                    work_days: parseInt(row['근무일수'] || row['WorkDays']) || 0,
                    base_salary_str: formatNumber(parseFloat(String(row['기본급'] || row['BaseSalary'] || '0').replace(/,/g, ''))),
                    allowances_str: formatNumber(parseFloat(String(row['수당총계'] || row['Allowances'] || '0').replace(/,/g, ''))),
                    gross_pay_str: formatNumber(parseFloat(String(row['총지급액'] || row['GrossPay'] || '0').replace(/,/g, ''))),
                    deductions_str: formatNumber(parseFloat(String(row['공제총계'] || row['Deductions'] || '0').replace(/,/g, ''))),
                    net_pay_str: formatNumber(parseFloat(String(row['실수령액'] || row['NetPay'] || '0').replace(/,/g, ''))),
                    remarks: String(row['비고'] || row['Remarks'] || ''),
                    sms_sent_status: String(row['SMS상태'] || row['SMSStatus'] || 'pending').toLowerCase(),
                    phone_number: String(row['전화번호'] || row['PhoneNumber'] || ''),
                    // Add numeric fields if your offcanvases or internal logic uses them directly
                    numeric_base_salary: parseFloat(String(row['기본급'] || row['BaseSalary'] || '0').replace(/,/g, '')),
                    numeric_total_allowances: parseFloat(String(row['수당총계'] || row['Allowances'] || '0').replace(/,/g, '')),
                    numeric_total_deductions: parseFloat(String(row['공제총계'] || row['Deductions'] || '0').replace(/,/g, '')),
                    numeric_net_pay: parseFloat(String(row['실수령액'] || row['NetPay'] || '0').replace(/,/g, '')),
                    // For detailed items, you'd need more complex CSV structure or parsing
                    allowance_items: [], // Placeholder
                    deduction_items: [], // Placeholder
                    sms_details: null    // Placeholder
                };
            }).filter(row => row.id && row.name);

            if (mappedPayrollData.length > 0) {
                $("#payrollJqxGrid").jqxGrid('beginupdate');
                mappedPayrollData.forEach(newRow => { $("#payrollJqxGrid").jqxGrid('addrow', newRow.uid, newRow); });
                $("#payrollJqxGrid").jqxGrid('endupdate');
                alert(mappedPayrollData.length + " {{ __('employee.management.records_imported_successfully') }}");
            } else { alert("{{ __('employee.management.no_valid_data_to_import') }}"); }
            $('#csvPreviewArea').hide(); $('#excelFile').val('');
        });
        $('#downloadTemplateBtn').on('click', function() { /* ... same download template logic ... */
            var csvHeaders = ["사원번호", "부서", "직책", "이름", "근무일수", "기본급", "수당총계", "총지급액", "공제총계", "실수령액", "비고", "SMS상태(sent/pending/failed)", "전화번호"];
            var csvContent = csvHeaders.join(",") + "\n";
            var blob = new Blob(["\ufeff" + csvContent], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement("a");
            if (link.download !== undefined) {
                var url = URL.createObjectURL(blob); link.setAttribute("href", url); link.setAttribute("download", "payroll_upload_template.csv");
                link.style.visibility = 'hidden'; document.body.appendChild(link); link.click(); document.body.removeChild(link);
            } else { alert("{{ __('employee.management.browser_not_support_download') }}"); }
        });

        // Offcanvas Form Submissions
        $('#attendanceLeaveForm').on('submit', function(e) { e.preventDefault(); alert('{{ __("offcanvas.attendance.submit_feedback") }}'); bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasAttendanceLeave'))?.hide(); });
        $('#payrollDetailsForm').on('submit', function(e) { e.preventDefault(); alert('Modify logic for payroll details to update grid not implemented.'); bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasPayrollDetails'))?.hide(); });

        // SMS Preview Offcanvas Logic
        const offcanvasSmsPreviewInstance = new bootstrap.Offcanvas(document.getElementById('offcanvasSmsPreview'));
        $('.btn-send-sms-custom').on('click', function() {
            var selectedRowsData = [];
            var selectedIndexes = $('#payrollJqxGrid').jqxGrid('getselectedrowindexes');
            selectedIndexes.forEach(index => {
                // Ensure the row data fetched includes the sms_details
                var rowData = $('#payrollJqxGrid').jqxGrid('getrowdata', index);
                if (rowData) { // Check if rowData is not undefined
                    selectedRowsData.push(rowData);
                }
            });

            if (selectedRowsData.length === 0) {
                alert("{{ __('payroll.actions.select_employee_for_sms') }}");
                return;
            }
            populateSmsEmployeeList(selectedRowsData);
            $('#smsSenderNumberValue').text('070-5555-3333');
            $('#smsRemainingPointsValue').text(formatNumber(40080));
            offcanvasSmsPreviewInstance.show();
        });

        let currentSmsPreviewList = [];
        function populateSmsEmployeeList(selectedEmployees) {
            currentSmsPreviewList = selectedEmployees;
            const listContainer = $('#employeeSmsListTableBody');
            listContainer.empty();
            $('#smsSelectedEmployeesCount').text("{{ __('payroll.offcanvas_sms.selected_employees_count') }}".replace(':count', selectedEmployees.length));

            if (selectedEmployees.length === 0) {
                listContainer.append('<tr><td colspan="4" class="text-center text-muted small">No employees selected.</td></tr>');
                updateSmsPreviewDisplay(null); // Pass null to clear preview
                return;
            }
            selectedEmployees.forEach((emp, index) => {
                const row = $(`<tr data-row-uid="${emp.uid}"><td>${emp.id}</td><td>${emp.department}</td><td>${emp.name}</td><td>${emp.phone_number || '-'}</td></tr>`);
                if (index === 0) {
                    row.addClass('active-sms-employee');
                    updateSmsPreviewDisplay(emp); // Initial preview for the first selected
                }
                listContainer.append(row);
            });
        }

        $('#employeeSmsListTableBody').on('click', 'tr', function() {
            const rowUID = $(this).data('row-uid');
            if (!rowUID) return;
            const employeeData = currentSmsPreviewList.find(emp => emp.uid === rowUID);
            $('#employeeSmsListTableBody tr').removeClass('active-sms-employee');
            $(this).addClass('active-sms-employee');
            updateSmsPreviewDisplay(employeeData);
        });

        function updateSmsPreviewDisplay(employeeData) {
            const contactNameEl = $('#smsPreviewContactName');
            const messageContentEl = $('#smsPreviewMessageContent');
            const templateTextareaEl = $('#smsMessageTemplateTextarea');

            messageContentEl.empty(); // Clear previous messages

            if (!employeeData || !employeeData.sms_details) {
                contactNameEl.text('N/A');
                messageContentEl.html('<div class="d-flex justify-content-center align-items-center h-100"><p class="text-muted small">Select an employee to see SMS details.</p></div>');
                templateTextareaEl.val('');
                return;
            }

            const sms = employeeData.sms_details;
            contactNameEl.text(employeeData.name); // Set contact name at the top of phone preview

            // Timestamp for the message group
            messageContentEl.append(`<p class="sms-timestamp">{{ __('payroll.offcanvas_sms.sms_timestamp_today') }} ${new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true })}</p>`);

            // Main Message Bubble
            let bubbleHTML = `<div class="sms-bubble-wrapper received"><div class="sms-bubble received">`; // Assuming payroll messages are "received" by employee
            bubbleHTML += `<strong>[${sms.company_name || 'Your Company'}]</strong><br>`;
            bubbleHTML += `${sms.intro_line1 || ''}<br>`;
            bubbleHTML += `${sms.intro_line2 || ''}<br>`;
            bubbleHTML += `<a href="${sms.link_url || '#'}" target="_blank">${sms.link_text || 'View Statement'}</a>`;

            // Payroll Info Block within the same bubble
            bubbleHTML += `<div class="payroll-info-block">`;
            bubbleHTML += `<div class="sms-item"><span class="sms-item-label">{{ __('payroll.offcanvas_sms.sms_payment_date_label') }}:</span> <span class="sms-item-value">${sms.payment_date || 'N/A'}</span></div>`;
            bubbleHTML += `<div class="sms-item"><span class="sms-item-label">{{ __('payroll.offcanvas_sms.sms_emp_code_label') }}:</span> <span class="sms-item-value">${employeeData.id || 'N/A'}</span></div>`;
            bubbleHTML += `<div class="sms-item"><span class="sms-item-label">{{ __('payroll.offcanvas_sms.sms_emp_name_label') }}:</span> <span class="sms-item-value">${employeeData.name || 'N/A'} quý vị</span></div>`;
            bubbleHTML += `<br><strong>${sms.statement_title || 'Payroll Details'}</strong><br>`;

            if (sms.earnings && Array.isArray(sms.earnings) && sms.earnings.length > 0) {
                sms.earnings.forEach(e => {
                    bubbleHTML += `<div class="sms-item"><span class="sms-item-label">${e.label || 'Earning'}:</span> <span class="sms-item-value">${formatNumber(e.value)}</span></div>`;
                });
            }
            bubbleHTML += `<div class="sms-total"><span class="sms-item-label">${sms.total_gross_pay_label || 'Total Gross'}:</span> <span class="sms-item-value">${formatNumber(sms.total_gross_pay_value)}</span></div>`;

            if (sms.deductions && Array.isArray(sms.deductions) && sms.deductions.length > 0) {
                bubbleHTML += `<br>`; // Space before deductions
                sms.deductions.forEach(d => {
                    bubbleHTML += `<div class="sms-item"><span class="sms-item-label">${d.label || 'Deduction'}:</span> <span class="sms-item-value">${formatNumber(d.value)}</span></div>`;
                });
            }
            bubbleHTML += `<div class="sms-total"><span class="sms-item-label">${sms.total_deductions_label || 'Total Deductions'}:</span> <span class="sms-item-value">${formatNumber(sms.total_deductions_value)}</span></div>`;
            bubbleHTML += `</div>`; // Close payroll-info-block
            bubbleHTML += `</div></div>`; // Close sms-bubble and sms-bubble-wrapper

            messageContentEl.append(bubbleHTML);

            // Populate Textarea (Simplified for example)
            let templateText = `[${sms.company_name || 'Your Company'}] ${sms.intro_line1 || ''}\n`;
            templateText += `Link: ${sms.link_url || '#'}\n\n`;
            templateText += `Dear ${employeeData.name || 'Employee'},\nYour payslip for ${currentPayrollMonthJS} is ready. Gross: ${formatNumber(sms.total_gross_pay_value)}, Deductions: ${formatNumber(sms.total_deductions_value)}.`;
            templateTextareaEl.val(templateText);
        }

        $('#smsEmployeeSearchInput').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            $('#employeeSmsListTableBody tr').each(function() {
                $(this).toggle($(this).text().toLowerCase().includes(searchTerm));
            });
        });

        $('#smsPreviewForm').on('submit', function(e) {
            e.preventDefault();
            const activeEmployeeRow = $('#employeeSmsListTableBody tr.active-sms-employee');
            if (activeEmployeeRow.length === 0) {
                alert("Please select an employee from the list.");
                return;
            }
            // Actual SMS sending logic would go here
            alert('SMS sending functionality not implemented in this example.');
        });

        // Global Functions for Modal Operations
        window.openCreatePayrollModal = function() {
            const modal = new bootstrap.Modal($('#payrollModal')[0]);
            const form = $('#payrollForm');
            
            // Reset form
            form[0].reset();
            form.removeData('edit-mode').removeData('payroll-id');
            $('#payrollModalTitle').text('{{ __("payroll.create_new") }}');
            $('#savePayrollBtn').text('{{ __("app.create") }}');
            
            // Reset employee selection
            $('#modalEmployeeSelect').val('');
            clearEmployeeFields();
            
            // Reset allowance and deduction containers
            resetAllowanceDeductionContainers();
            calculateTotals();
            updateRemoveButtonsVisibility();
            
            modal.show();
        };

        window.openImportModal = function() {
            const modal = new bootstrap.Modal($('#importModal')[0]);
            $('#importFile').val('');
            $('#importPreview').addClass('d-none');
            modal.show();
        };

        // Helper Functions
        function clearEmployeeFields() {
            $('#modalEmployeeId, #modalEmployeeName, #modalDepartment, #modalPosition, #modalPhoneNumber').val('');
        }

        function resetAllowanceDeductionContainers() {
            // Reset allowances container
            $('#allowancesContainer').html(`
                <div class="allowance-item row mb-2">
                    <div class="col-md-6">
                        <select class="form-select allowance-type" name="allowance_type[]">
                            <option value="">{{ __('payroll.select_allowance_type') }}</option>
                            <option value="seniority">{{ __('payroll.offcanvas_details.allowances.seniority') }}</option>
                            <option value="position">{{ __('payroll.offcanvas_details.allowances.position') }}</option>
                            <option value="job">{{ __('payroll.offcanvas_details.allowances.job') }}</option>
                            <option value="overtime">{{ __('payroll.offcanvas_details.allowances.overtime') }}</option>
                            <option value="transportation">{{ __('payroll.offcanvas_details.allowances.transportation') }}</option>
                            <option value="meal">{{ __('payroll.offcanvas_details.allowances.meal') }}</option>
                            <option value="other">{{ __('payroll.offcanvas_details.allowances.other') }}</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control allowance-amount" name="allowance_amount[]" placeholder="{{ __('payroll.amount') }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-allowance" style="display: none;">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                </div>
            `);
            
            // Reset deductions container
            $('#deductionsContainer').html(`
                <div class="deduction-item row mb-2">
                    <div class="col-md-6">
                        <select class="form-select deduction-type" name="deduction_type[]">
                            <option value="">{{ __('payroll.select_deduction_type') }}</option>
                            <option value="health_insurance">{{ __('payroll.offcanvas_details.deductions.health_insurance') }}</option>
                            <option value="long_term_care_insurance">{{ __('payroll.offcanvas_details.deductions.long_term_care_insurance') }}</option>
                            <option value="employment_insurance">{{ __('payroll.offcanvas_details.deductions.employment_insurance') }}</option>
                            <option value="national_pension">{{ __('payroll.offcanvas_details.deductions.national_pension') }}</option>
                            <option value="income_tax">{{ __('payroll.offcanvas_details.deductions.income_tax') }}</option>
                            <option value="local_income_tax">{{ __('payroll.offcanvas_details.deductions.local_income_tax') }}</option>
                            <option value="other">{{ __('payroll.offcanvas_details.deductions.other') }}</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control deduction-amount" name="deduction_amount[]" placeholder="{{ __('payroll.amount') }}" min="0" step="0.01">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-deduction" style="display: none;">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                </div>
            `);
        }

        function calculateTotals() {
            // Calculate total allowances
            let totalAllowances = 0;
            $('#allowancesContainer .allowance-amount').each(function() {
                const amount = parseFloat($(this).val()) || 0;
                totalAllowances += amount;
            });
            $('#totalAllowances').text(totalAllowances.toLocaleString());
            
            // Calculate total deductions
            let totalDeductions = 0;
            $('#deductionsContainer .deduction-amount').each(function() {
                const amount = parseFloat($(this).val()) || 0;
                totalDeductions += amount;
            });
            $('#totalDeductions').text(totalDeductions.toLocaleString());
            
            // Calculate gross pay and net pay
            const baseSalary = parseFloat($('#modalBaseSalary').val()) || 0;
            const grossPay = baseSalary + totalAllowances;
            const netPay = grossPay - totalDeductions;
            
            $('#modalGrossPay').val(grossPay.toFixed(2));
            $('#modalNetPay').val(netPay.toFixed(2));
        }

        function updateRemoveButtonsVisibility() {
            // Show/hide remove buttons for allowances
            const allowanceItems = $('#allowancesContainer .allowance-item');
            allowanceItems.find('.remove-allowance').toggle(allowanceItems.length > 1);
            
            // Show/hide remove buttons for deductions  
            const deductionItems = $('#deductionsContainer .deduction-item');
            deductionItems.find('.remove-deduction').toggle(deductionItems.length > 1);
        }

        // Modal Event Handlers
        $('#savePayrollBtn').on('click', function() {
            const form = $('#payrollForm');
            
            // Basic validation
            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return;
            }
            
            // Collect allowance items
            const allowanceItems = [];
            $('#allowancesContainer .allowance-item').each(function() {
                const type = $(this).find('.allowance-type').val();
                const amount = parseFloat($(this).find('.allowance-amount').val()) || 0;
                if (type && amount > 0) {
                    allowanceItems.push({ type: type, amount: amount });
                }
            });
            
            // Collect deduction items
            const deductionItems = [];
            $('#deductionsContainer .deduction-item').each(function() {
                const type = $(this).find('.deduction-type').val();
                const amount = parseFloat($(this).find('.deduction-amount').val()) || 0;
                if (type && amount > 0) {
                    deductionItems.push({ type: type, amount: amount });
                }
            });
            
            const formData = {
                employee_id: $('#modalEmployeeId').val(),
                name: $('#modalEmployeeName').val(),
                department: $('#modalDepartment').val(),
                position: $('#modalPosition').val(),
                phone_number: $('#modalPhoneNumber').val(),
                work_days: parseInt($('#modalWorkDays').val()) || 0,
                base_salary: parseFloat($('#modalBaseSalary').val()) || 0,
                allowance_items: allowanceItems,
                deduction_items: deductionItems,
                remarks: $('#modalRemarks').val()
            };
            
            // Submit via AJAX
            $.ajax({
                url: '{{ route("payrolls.store") }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("payroll.created_successfully") }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        bootstrap.Modal.getInstance($('#payrollModal')[0])?.hide();
                        // Refresh the grid data instead of full page reload
                        refreshPayrollGrid();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || '{{ __("payroll.creation_failed") }}';
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("app.error") }}',
                        text: message
                    });
                }
            });
        });

        // Add/Remove Allowance Items
        $('#addAllowanceBtn').on('click', function() {
            addAllowanceItem();
        });

        $(document).on('click', '.remove-allowance', function() {
            $(this).closest('.allowance-item').remove();
            calculateTotals();
            updateRemoveButtonsVisibility();
        });

        // Add/Remove Deduction Items
        $('#addDeductionBtn').on('click', function() {
            addDeductionItem();
        });

        $(document).on('click', '.remove-deduction', function() {
            $(this).closest('.deduction-item').remove();
            calculateTotals();
            updateRemoveButtonsVisibility();
        });

        // Calculate totals when amounts change
        $(document).on('input', '.allowance-amount, .deduction-amount, #modalBaseSalary', function() {
            calculateTotals();
        });

        // Employee Selection Handler
        $('#modalEmployeeSelect').on('change', function() {
            const employeeId = $(this).val();
            if (employeeId) {
                loadEmployeeData(employeeId);
            } else {
                clearEmployeeFields();
            }
        });

        // Load employees when modal opens
        $('#payrollModal').on('show.bs.modal', function() {
            loadEmployees();
        });

        // Employee management functions
        function loadEmployees() {
            $.ajax({
                url: '{{ route("payrolls.employees") }}',
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    const select = $('#modalEmployeeSelect');
                    select.empty().append('<option value="">{{ __("payroll.select_employee_placeholder") }}</option>');
                    
                    if (response.employees && response.employees.length > 0) {
                        response.employees.forEach(function(employee) {
                            select.append(`<option value="${employee.id}">${employee.employee_id} - ${employee.name} (${employee.department})</option>`);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Failed to load employees:', xhr);
                }
            });
        }

        function loadEmployeeData(employeeId) {
            $.ajax({
                url: '{{ route("payrolls.employee", ":id") }}'.replace(':id', employeeId),
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.employee) {
                        const emp = response.employee;
                        $('#modalEmployeeId').val(emp.employee_id || '');
                        $('#modalEmployeeName').val(emp.name || '');
                        $('#modalDepartment').val(emp.department || '');
                        $('#modalPosition').val(emp.position || '');
                        $('#modalPhoneNumber').val(emp.phone_number || '');
                        $('#modalBaseSalary').val(emp.base_salary || '');
                        calculateTotals();
                    }
                },
                error: function(xhr) {
                    console.error('Failed to load employee data:', xhr);
                    clearEmployeeFields();
                }
            });
        }

        function addAllowanceItem() {
            const container = $('#allowancesContainer');
            const newItem = $(`
                <div class="allowance-item row mb-2">
                    <div class="col-md-6">
                        <select class="form-select allowance-type" name="allowance_type[]">
                            <option value="">{{ __('payroll.select_allowance_type') }}</option>
                            <option value="seniority">{{ __('payroll.offcanvas_details.allowances.seniority') }}</option>
                            <option value="position">{{ __('payroll.offcanvas_details.allowances.position') }}</option>
                            <option value="job">{{ __('payroll.offcanvas_details.allowances.job') }}</option>
                            <option value="overtime">{{ __('payroll.offcanvas_details.allowances.overtime') }}</option>
                            <option value="holiday_special_work">{{ __('payroll.offcanvas_details.allowances.holiday_special_work') }}</option>
                            <option value="night_shift">{{ __('payroll.offcanvas_details.allowances.night_shift') }}</option>
                            <option value="bonus">{{ __('payroll.offcanvas_details.allowances.bonus') }}</option>
                            <option value="adjustment">{{ __('payroll.offcanvas_details.allowances.adjustment') }}</option>
                            <option value="transportation">{{ __('payroll.offcanvas_details.allowances.transportation') }}</option>
                            <option value="meal">{{ __('payroll.offcanvas_details.allowances.meal') }}</option>
                            <option value="labor_day">{{ __('payroll.offcanvas_details.allowances.labor_day') }}</option>
                            <option value="annual_leave">{{ __('payroll.offcanvas_details.allowances.annual_leave') }}</option>
                            <option value="welfare">{{ __('payroll.offcanvas_details.allowances.welfare') }}</option>
                            <option value="other">{{ __('payroll.offcanvas_details.allowances.other') }}</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control allowance-amount" name="allowance_amount[]" placeholder="{{ __('payroll.amount') }}" min="0" step="0.01">
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
                        <select class="form-select deduction-type" name="deduction_type[]">
                            <option value="">{{ __('payroll.select_deduction_type') }}</option>
                            <option value="health_insurance">{{ __('payroll.offcanvas_details.deductions.health_insurance') }}</option>
                            <option value="long_term_care_insurance">{{ __('payroll.offcanvas_details.deductions.long_term_care_insurance') }}</option>
                            <option value="employment_insurance">{{ __('payroll.offcanvas_details.deductions.employment_insurance') }}</option>
                            <option value="national_pension">{{ __('payroll.offcanvas_details.deductions.national_pension') }}</option>
                            <option value="income_tax">{{ __('payroll.offcanvas_details.deductions.income_tax') }}</option>
                            <option value="local_income_tax">{{ __('payroll.offcanvas_details.deductions.local_income_tax') }}</option>
                            <option value="other">{{ __('payroll.offcanvas_details.deductions.other') }}</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control deduction-amount" name="deduction_amount[]" placeholder="{{ __('payroll.amount') }}" min="0" step="0.01">
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

    }); // End document.ready
  </script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="page-header">
        <div><h1 class="header-main-title mb-1">{{ $image_page_title }}</h1><div class="header-sub-info"><span>{{ $image_total_employees_text }}</span><span>{{ $image_sms_sent_employees_text }}</span></div></div>
    </div>

    <div class="excel-upload-area">
        <div class="upload-icon"></div>
        <p>{{ $image_excel_upload_prompt }}</p>
        <input type="file" id="excelFile" accept=".csv" style="display: none;" />
        <button type="button" class="btn btn-outline-primary btn-sm mt-2 me-2" onclick="document.getElementById('excelFile').click();"><i class="bx bx-file-find me-1"></i>{{ __('employee.management.browse_excel_button') }}</button>
        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="downloadTemplateBtn"><i class="bx bx-download me-1"></i>{{ __('employee.management.download_template_button') }}</button>
    </div>

    <div id="csvPreviewArea" style="display: none;" class="card mt-3 mb-3">
        <div class="card-header d-flex justify-content-between align-items-center py-2"><h5 id="csvPreviewFileName" class="mb-0 card-title" style="font-size: 1rem;"></h5><div><button type="button" class="btn btn-success btn-sm me-2" id="importCsvDataBtn"><i class="bx bx-import me-1"></i>{{ __('employee.management.import_data_button') }}</button><button type="button" class="btn btn-danger btn-sm" id="cancelCsvPreviewBtn"><i class="bx bx-x me-1"></i>{{ __('app.cancel_button') }}</button></div></div>
        <div class="card-body p-0"><div class="table-responsive" style="max-height: 350px;"><table class="table table-bordered table-sm table-hover mb-0" id="csvPreviewTable"><thead></thead><tbody></tbody></table></div></div>
    </div>

    <div class="actions-bar">
        <div class="actions-bar-left btn-group" role="group">
            <button class="btn btn-action-filter btn-sm" data-datafield="department" data-filter-value="SETEC">{{ $image_filter_site_button }}</button>
            <button class="btn btn-action-filter btn-sm" data-datafield="payroll_month_field" data-filter-value="{{ $currentPayrollMonth }}">{{ $image_filter_month_button }}</button>
        </div>
        <div class="actions-bar-right">
            <span id="selectedCountDisplay" class="selected-count-display">{{ $image_selected_count_text }}</span>
            <button class="btn btn-success btn-sm me-2" onclick="openCreatePayrollModal()">
                <i class="bx bx-plus me-1"></i>{{ __('payroll.create_new') }}
            </button>
            <button class="btn btn-primary btn-sm me-2" onclick="openImportModal()">
                <i class="bx bx-import me-1"></i>{{ __('payroll.import_title') }}
            </button>
            <button class="btn btn-send-sms-custom btn-sm"><i class="bx bx-mail-send me-1"></i>{{ $image_send_sms_button_text }}</button>
            <div class="input-group input-group-sm search-action-group" style="width: 220px;">
                 <span class="input-group-text"><i class="bx bx-search"></i></span>
                 <input type="text" class="form-control form-control-sm" id="globalTableSearchInput" placeholder="{{ __('datatables.searchPlaceholderShort') }}">
            </div>
        </div>
    </div>

    <div id="payrollJqxGrid" class="mt-3"></div>

    @if(empty($payrollEntriesData))
    <div id="emptyPayrollState" class="text-center py-5">
        <div class="mb-4">
            <i class="bx bx-receipt" style="font-size: 4rem; color: #6c757d;"></i>
        </div>
        <h5 class="text-muted mb-3">{{ __('payroll.no_payroll_records') }}</h5>
        <p class="text-muted mb-4">{{ __('payroll.no_payroll_records_description') }}</p>
        <button class="btn btn-primary" onclick="openCreatePayrollModal()">
            <i class="bx bx-plus me-2"></i>{{ __('payroll.create_first_payroll') }}
        </button>
    </div>
    @endif

</div>

{{-- Offcanvas for Attendance/Leave --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAttendanceLeave" aria-labelledby="offcanvasAttendanceLeaveLabel" data-bs-backdrop="static" data-example-attendance="{{ $jsonEncodedAttendanceData }}">
    <div class="offcanvas-header-custom"><div><h5 id="offcanvasAttendanceLeaveLabelEmployee" class="offcanvas-title-main mb-0"></h5><div id="offcanvasAttendanceLeaveLabelMonth" class="offcanvas-title-sub"></div></div><button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0"><form id="attendanceLeaveForm" onsubmit="return false;"><div class="work-days-input-group"><label for="offcanvasWorkDaysInput" class="form-label mb-0">{{ __('offcanvas.attendance.work_days_input_label') }}</label><input type="number" class="form-control form-control-sm" id="offcanvasWorkDaysInput" name="work_days_override" placeholder="{{ __('offcanvas.attendance.work_days_placeholder') }}"><small class="ms-2 text-muted">{{ __('offcanvas.attendance.days_unit') }}</small></div><div class="attendance-table-container"><table class="table table-sm table-bordered attendance-table"><thead class="table-light"><tr><th>{{ __('offcanvas.attendance.table.header.type') }}</th><th>{{ __('offcanvas.attendance.table.header.date') }}</th><th>{{ __('offcanvas.attendance.table.header.period') }}</th><th>{{ __('offcanvas.attendance.table.header.paid_status') }}</th><th>{{ __('offcanvas.attendance.table.header.memo') }}</th></tr></thead><tbody id="attendanceLeaveTableBody"></tbody></table></div></form></div>
    <div class="offcanvas-footer-custom text-center"><button type="submit" form="attendanceLeaveForm" class="btn btn-primary btn-submit-attendance w-100">{{ __('offcanvas.attendance.submit_button') }}</button></div>
</div>

{{-- Offcanvas for Payroll Details --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPayrollDetails" aria-labelledby="offcanvasPayrollDetailsLabel" data-bs-backdrop="static" style="width: 480px;">
    <div class="offcanvas-header"><h5 class="offcanvas-title" id="offcanvasPayrollDetailsTitle"></h5><button type="button" class="btn-close btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
    <div class="offcanvas-body"><form id="payrollDetailsForm" onsubmit="return false;"><div class="base-salary-item"><span id="payrollDetailBaseSalaryLabel" class="label"></span><span id="payrollDetailBaseSalaryAmount" class="amount"></span></div><div class="payroll-items-grid"><div class="payroll-detail-section allowances-section"><div class="section-title"><span>{{ __('payroll.offcanvas_details.allowances_section_title') }}</span><span id="payrollDetailAllowancesTotal" class="total-amount"></span></div><table class="payroll-items-table"> <tbody id="payrollDetailAllowancesTableBody"></tbody> </table></div><div class="payroll-detail-section deductions-section"><div class="section-title"><span>{{ __('payroll.offcanvas_details.deductions_section_title') }}</span><span id="payrollDetailDeductionsTotal" class="total-amount"></span></div><table class="payroll-items-table"> <tbody id="payrollDetailDeductionsTableBody"></tbody> </table></div></div></form></div>
    <div class="offcanvas-footer">
        <button type="submit" form="payrollDetailsForm" class="btn btn-primary btn-modify-payroll">{{ __('payroll.offcanvas_details.modify_button') }}</button>
        <a href="#" class="btn btn-print-payroll btn-secondary"><i class="bx bx-printer me-1"></i>{{ __('payroll.offcanvas_details.print_button') }}</a>
    </div>
</div>

{{-- Offcanvas for SMS Preview & Send --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSmsPreview" aria-labelledby="offcanvasSmsPreviewLabel" data-bs-backdrop="static" style="width: 750px;">
    <div class="offcanvas-header"><h5 class="offcanvas-title" id="offcanvasSmsPreviewLabel">{{ __('payroll.offcanvas_sms.title') }}</h5><button type="button" class="btn-close btn-close-sms-custom" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>
    <div class="offcanvas-body">
        <div class="sms-offcanvas-left-panel">
            <div id="smsSelectedEmployeesCount" class="selected-info"></div>
            <div class="search-employee-sms"><input type="text" class="form-control form-control-sm" id="smsEmployeeSearchInput" placeholder="{{ __('payroll.offcanvas_sms.search_placeholder') }}"></div>
            <div class="employee-sms-list-container"><table class="employee-sms-list-table"><thead><tr><th>{{ __('payroll.offcanvas_sms.employee_list_header_id') }}</th><th>{{ __('payroll.offcanvas_sms.employee_list_header_department') }}</th><th>{{ __('payroll.offcanvas_sms.employee_list_header_name') }}</th><th>{{ __('payroll.offcanvas_sms.employee_list_header_phone') }}</th></tr></thead><tbody id="employeeSmsListTableBody"></tbody></table></div>
        </div>
        <div class="sms-offcanvas-right-panel">
            {{-- This is the phone structure from the image --}}
            <div class="phone-preview-outer">
                <div class="phone-preview-container">
                    <div class="phone-preview-notch"></div>
                    <div class="phone-preview-top-bar">
                        <span class="time">9:41 AM</span>
                        <span class="status-icons">
                            <i class="bx bx-wifi"></i> <i class="bx bxs-battery"></i>
                        </span>
                    </div>
                    <div class="phone-preview-contact-header">
                        <span class="back-arrow">〈</span>
                        <div class="contact-name-details">
                            <div id="smsPreviewContactName" class="contact-name">{{ __('payroll.offcanvas_sms.sms_recipient_label') }}</div>
                            {{-- <div class="contact-subtext">mobile</div> --}}
                        </div>
                    </div>
                    <div id="smsPreviewMessageContent" class="phone-preview-messages">
                        {{-- Bubbles will be injected here by JS --}}
                    </div>
                </div>
            </div>
            {{-- Controls below the phone --}}
            <div class="sms-controls-area">
                <form id="smsPreviewForm" onsubmit="return false;">
                     <div class="sms-message-template-area">
                        <label for="smsMessageTemplateTextarea">{{ __('payroll.offcanvas_sms.message_template_label') }}</label>
                        <textarea class="form-control form-control-sm" id="smsMessageTemplateTextarea" rows="3"></textarea>
                    </div>
                    <div class="sms-info-group">
                        <span class="label">{{ __('payroll.offcanvas_sms.sender_number_label') }}:</span> <span id="smsSenderNumberValue" class="value"></span>
                    </div>
                    <div class="sms-info-group">
                        <span class="label">{{ __('payroll.offcanvas_sms.remaining_points_label') }}:</span> <span id="smsRemainingPointsValue" class="value"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer">
        <button type="submit" form="smsPreviewForm" class="btn btn-primary btn-resend-sms w-100"><i class="bx bx-send me-1"></i>{{ __('payroll.offcanvas_sms.resend_sms_button') }}</button>
    </div>
</div>

{{-- Modal for Create/Edit Payroll --}}
<div class="modal fade" id="payrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payrollModalTitle">{{ __('payroll.create_new') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="payrollForm" class="row g-3">
                    <!-- Employee Selection -->
                    <div class="col-12">
                        <label for="modalEmployeeSelect" class="form-label">{{ __('payroll.select_employee') }}</label>
                        <select id="modalEmployeeSelect" class="form-select" required>
                            <option value="">{{ __('payroll.select_employee_placeholder') }}</option>
                        </select>
                        <div class="form-text">{{ __('payroll.employee_selection_help') }}</div>
                    </div>
                    
                    <!-- Employee Information (auto-filled) -->
                    <div class="col-md-6">
                        <label for="modalEmployeeId" class="form-label">{{ __('payroll.employee_id') }}</label>
                        <input type="text" id="modalEmployeeId" name="employee_id" class="form-control" readonly required>
                    </div>
                    <div class="col-md-6">
                        <label for="modalEmployeeName" class="form-label">{{ __('payroll.employee_name') }}</label>
                        <input type="text" id="modalEmployeeName" name="name" class="form-control" readonly required>
                    </div>
                    <div class="col-md-6">
                        <label for="modalDepartment" class="form-label">{{ __('payroll.department') }}</label>
                        <input type="text" id="modalDepartment" name="department" class="form-control" readonly required>
                    </div>
                    <div class="col-md-6">
                        <label for="modalPosition" class="form-label">{{ __('payroll.position') }}</label>
                        <input type="text" id="modalPosition" name="position" class="form-control" readonly required>
                    </div>
                    <div class="col-md-6">
                        <label for="modalPhoneNumber" class="form-label">{{ __('payroll.phone_number') }}</label>
                        <input type="text" id="modalPhoneNumber" name="phone_number" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="modalWorkDays" class="form-label">{{ __('payroll.work_days') }}</label>
                        <input type="number" id="modalWorkDays" name="work_days" class="form-control" min="0" max="31" required>
                    </div>
                    
                    <!-- Base Salary -->
                    <div class="col-12">
                        <label for="modalBaseSalary" class="form-label">{{ __('payroll.base_salary') }}</label>
                        <input type="number" id="modalBaseSalary" name="base_salary" class="form-control" min="0" step="0.01" required>
                    </div>

                    <!-- Allowances Section -->
                    <div class="col-12">
                        <hr>
                        <h6 class="mb-3">{{ __('payroll.allowances_detail') }}</h6>
                        <div id="allowancesContainer">
                            <div class="allowance-item row mb-2">
                                <div class="col-md-6">
                                    <select class="form-select allowance-type" name="allowance_type[]">
                                        <option value="">{{ __('payroll.select_allowance_type') }}</option>
                                        <option value="seniority">{{ __('payroll.offcanvas_details.allowances.seniority') }}</option>
                                        <option value="position">{{ __('payroll.offcanvas_details.allowances.position') }}</option>
                                        <option value="job">{{ __('payroll.offcanvas_details.allowances.job') }}</option>
                                        <option value="overtime">{{ __('payroll.offcanvas_details.allowances.overtime') }}</option>
                                        <option value="holiday_special_work">{{ __('payroll.offcanvas_details.allowances.holiday_special_work') }}</option>
                                        <option value="night_shift">{{ __('payroll.offcanvas_details.allowances.night_shift') }}</option>
                                        <option value="bonus">{{ __('payroll.offcanvas_details.allowances.bonus') }}</option>
                                        <option value="adjustment">{{ __('payroll.offcanvas_details.allowances.adjustment') }}</option>
                                        <option value="transportation">{{ __('payroll.offcanvas_details.allowances.transportation') }}</option>
                                        <option value="meal">{{ __('payroll.offcanvas_details.allowances.meal') }}</option>
                                        <option value="labor_day">{{ __('payroll.offcanvas_details.allowances.labor_day') }}</option>
                                        <option value="annual_leave">{{ __('payroll.offcanvas_details.allowances.annual_leave') }}</option>
                                        <option value="welfare">{{ __('payroll.offcanvas_details.allowances.welfare') }}</option>
                                        <option value="other">{{ __('payroll.offcanvas_details.allowances.other') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" class="form-control allowance-amount" name="allowance_amount[]" placeholder="{{ __('payroll.amount') }}" min="0" step="0.01">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-allowance" style="display: none;">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addAllowanceBtn" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>{{ __('payroll.add_allowance') }}
                        </button>
                        <div class="mt-2">
                            <strong>{{ __('payroll.total_allowances') }}: <span id="totalAllowances">0</span></strong>
                        </div>
                    </div>

                    <!-- Deductions Section -->
                    <div class="col-12">
                        <hr>
                        <h6 class="mb-3">{{ __('payroll.deductions_detail') }}</h6>
                        <div id="deductionsContainer">
                            <div class="deduction-item row mb-2">
                                <div class="col-md-6">
                                    <select class="form-select deduction-type" name="deduction_type[]">
                                        <option value="">{{ __('payroll.select_deduction_type') }}</option>
                                        <option value="health_insurance">{{ __('payroll.offcanvas_details.deductions.health_insurance') }}</option>
                                        <option value="long_term_care_insurance">{{ __('payroll.offcanvas_details.deductions.long_term_care_insurance') }}</option>
                                        <option value="employment_insurance">{{ __('payroll.offcanvas_details.deductions.employment_insurance') }}</option>
                                        <option value="national_pension">{{ __('payroll.offcanvas_details.deductions.national_pension') }}</option>
                                        <option value="income_tax">{{ __('payroll.offcanvas_details.deductions.income_tax') }}</option>
                                        <option value="local_income_tax">{{ __('payroll.offcanvas_details.deductions.local_income_tax') }}</option>
                                        <option value="other">{{ __('payroll.offcanvas_details.deductions.other') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" class="form-control deduction-amount" name="deduction_amount[]" placeholder="{{ __('payroll.amount') }}" min="0" step="0.01">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-deduction" style="display: none;">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addDeductionBtn" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-plus me-1"></i>{{ __('payroll.add_deduction') }}
                        </button>
                        <div class="mt-2">
                            <strong>{{ __('payroll.total_deductions') }}: <span id="totalDeductions">0</span></strong>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="col-12">
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modalGrossPay" class="form-label">{{ __('payroll.gross_pay') }}</label>
                                <input type="number" id="modalGrossPay" name="gross_pay" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="modalNetPay" class="form-label">{{ __('payroll.net_pay') }}</label>
                                <input type="number" id="modalNetPay" name="net_pay" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <label for="modalRemarks" class="form-label">{{ __('payroll.remarks') }}</label>
                        <textarea id="modalRemarks" name="remarks" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="savePayrollBtn">{{ __('app.save') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Import Payroll Data --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('payroll.import_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">{{ __('payroll.import_description') }}</p>
                <div class="mb-3">
                    <label for="importFile" class="form-label">{{ __('payroll.select_file') }}</label>
                    <input type="file" id="importFile" name="file" class="form-control" accept=".xlsx,.xls,.csv">
                    <div class="form-text">{{ __('payroll.supported_formats') }}</div>
                </div>
                <div id="importPreview" class="d-none">
                    <h6>{{ __('app.preview') }}</h6>
                    <div id="importPreviewContent"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.cancel') }}</button>
                <button type="button" class="btn btn-primary" id="importPayrollBtn">{{ __('payroll.import_button') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection
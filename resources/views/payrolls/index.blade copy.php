@extends('layouts.app')

@section('title', __('payroll.processing.title_main_suffix'))
@php
    $currentPayrollMonth = "2025년 3월";
    $totalPayrollEmployees = 25; // As per image
    $image_frame_label = "Frame 3"; // Label from initial image
    $image_page_title = $currentPayrollMonth . " " . __('payroll.processing.title_main_suffix');
    $image_total_employees_text = __('payroll.processing.total_employees_display_short', ['count' => $totalPayrollEmployees]);
    $image_sms_sent_employees_text = __('payroll.processing.sms_sent_employees_display', ['count' => $totalPayrollEmployees]); // Assuming all sent for initial display
    $image_excel_upload_prompt = __('payroll.processing.excel_upload_prompt_short');
    $image_filter_site_button = "SETEC"; // Hardcoded from image
    $image_filter_month_button = $currentPayrollMonth;
    $image_selected_count_text = __('payroll.actions.selected_count_display_short', ['count' => $totalPayrollEmployees]);
    $image_send_sms_button_text = __('payroll.actions.send_sms_selected_short');
    $image_search_label = __('app.search_short');
    $image_search_placeholder = ""; // Empty as per image

    // SMS details for '임채정' to match the SMS preview image
    $imChaeJeongSmsDetails = [
        'company_name' => __('payroll.offcanvas_sms.sms_company_name'),
        'intro_line1' => __('payroll.offcanvas_sms.sms_intro_line1'),
        'intro_line2' => __('payroll.offcanvas_sms.sms_intro_line2'),
        'link_url' => 'www.google.com/jaden221', // from image
        'link_text' => __('payroll.offcanvas_sms.sms_link_text'),
        'payment_date' => '2025년 04월 10일', // from image
        'statement_title' => __('payroll.offcanvas_sms.sms_statement_title_prefix').' '. __('payroll.offcanvas_sms.sms_statement_title_suffix'),
        'earnings' => [
            ['label' => __('payroll.offcanvas_sms.sms_base_salary_label'), 'value' => 3212000],
            ['label' => __('payroll.offcanvas_sms.sms_seniority_allowance_label'), 'value' => 400000],
            ['label' => __('payroll.offcanvas_sms.sms_annual_leave_allowance_label'), 'value' => 154000],
        ],
        'total_gross_pay_label' => __('payroll.offcanvas_sms.sms_total_gross_pay_label'),
        'total_gross_pay_value' => 3766000,
        'deductions' => [
            ['label' => __('payroll.offcanvas_sms.sms_health_insurance_label'), 'value' => 133500],
            ['label' => __('payroll.offcanvas_sms.sms_long_term_care_insurance_label'), 'value' => 17280],
            ['label' => __('payroll.offcanvas_sms.sms_income_tax_label'), 'value' => 178920],
            ['label' => __('payroll.offcanvas_sms.sms_local_income_tax_label'), 'value' => 17890],
        ],
        'total_deductions_label' => __('payroll.offcanvas_sms.sms_total_deductions_label'),
        'total_deductions_value' => 347590,
    ];

    // Main payroll data array
    $payrollEntriesForImageDisplay = [
        [
            'id' => '586', 'name' => '임채정', 'department' => '일신방직', 'position' => '미화', 'phone_number' => '010-5555-2222',
            'work_days' => 31, 'base_salary' => '2,299,000', 'allowances' => '358,190',
            'gross_pay' => '2,657,190', 'deductions' => '358,190', // From payroll details offcanvas image
            'net_pay' => '2,366,240',
            'remarks' => '국민연금 조기수령, 연금 공제 x', 'sms_sent_status' => 'sent', 'is_checked' => true,
            'numeric_base_salary' => 2299000, 'numeric_total_allowances' => 358190, 'numeric_total_deductions' => 358190,
            'allowance_items' => [ /* From previous setup */
                ['label_key' => 'payroll.offcanvas_details.allowances.seniority', 'label_translation' => __('payroll.offcanvas_details.allowances.seniority'), 'value' => 2299000],
                // ... other allowance items for ImChaeJeong ...
            ],
            'deduction_items' => [ /* From previous setup */
                ['label_key' => 'payroll.offcanvas_details.deductions.health_insurance', 'label_translation' => __('payroll.offcanvas_details.deductions.health_insurance'), 'value' => 2299000],
                 // ... other deduction items for ImChaeJeong ...
            ],
            'sms_details' => $imChaeJeongSmsDetails
        ],
        // Add 4 more entries for a total of 5 to match SMS list image, all checked for "25명 선택됨"
        [ 'id' => '586', 'name' => '임채정', 'department' => '일신방직', 'phone_number' => '010-5555-2222', /* other fields */ 'is_checked' => true, 'sms_details' => $imChaeJeongSmsDetails],
        [ 'id' => '586', 'name' => '임채정', 'department' => '일신방직', 'phone_number' => '010-5555-2222', /* other fields */ 'is_checked' => true, 'sms_details' => $imChaeJeongSmsDetails],
        [ 'id' => '586', 'name' => '임채정', 'department' => '일신방직', 'phone_number' => '010-5555-2222', /* other fields */ 'is_checked' => true, 'sms_details' => $imChaeJeongSmsDetails],
        [ 'id' => '586', 'name' => '임채정', 'department' => '일신방직', 'phone_number' => '010-5555-2222', /* other fields */ 'is_checked' => true, 'sms_details' => $imChaeJeongSmsDetails],
    ];
    // Fill remaining to reach 25 for the count, assuming they are checked
    for ($i = count($payrollEntriesForImageDisplay); $i < $totalPayrollEmployees; $i++) {
        $payrollEntriesForImageDisplay[] = [
            'id' => 'TEMP'.($i+1), 'name' => '사원 '.($i+1), 'department' => '임시부서', 'position' => '직원', 'phone_number' => '010-0000-0000',
            'work_days' => 30, 'base_salary' => '2,000,000', 'allowances' => '200,000', 'gross_pay' => '2,200,000', 'deductions' => '150,000', 'net_pay' => '2,050,000',
            'remarks' => '-', 'sms_sent_status' => 'pending', 'is_checked' => true,
            'numeric_base_salary' => 2000000, 'numeric_total_allowances' => 200000, 'numeric_total_deductions' => 150000,
            'allowance_items' => [], 'deduction_items' => [], 'sms_details' => null
        ];
    }


    $isSelectAllChecked = true; // Since "25명 선택됨" and total is 25

    $attendanceLeaveDataExample = [
        [ 'type' => __('offcanvas.attendance.table.header.type'), 'date' => '2025.03.25 - 2025.03.27', 'period' => '3일', 'paid' => 'X', 'memo' => '가족 행사'],
    ];
    $jsonEncodedAttendanceData = htmlspecialchars(json_encode($attendanceLeaveDataExample), ENT_QUOTES, 'UTF-8');
@endphp
@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
  <style>
    /* General Page Styles */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .header-main-title {
        font-size: 1.75rem;
        font-weight: bold;
        color: #333;
    }
    .header-sub-info {
        display: flex;
        gap: 1.5rem;
        font-size: 0.9rem;
        color: #555;
        margin-top: 0.25rem;
    }
    .excel-upload-area {
      border: 2px dashed #cccccc;
      padding: 25px 20px;
      text-align: center;
      margin-bottom: 20px;
      background-color: #f9f9f9;
      border-radius: 4px;
    }
    .upload-icon {
      width: 32px;
      height: 32px;
      background-color: #d0d0d0;
      margin: 0 auto 10px auto;
      border-radius: 3px;
    }
    .excel-upload-area p {
        margin-bottom: 0;
        color: #555;
        font-size: 0.9rem;
    }

    /* Actions Bar */
    .actions-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .actions-bar-left, .actions-bar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-action-filter {
        background-color: #e9ecef;
        border-color: #ced4da;
        color: #495057;
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
    }
    .selected-count-display {
        font-size: 0.85rem;
        color: #333;
        margin-right: 0.5rem;
    }
    .btn-send-sms-custom {
        background-color: #20c997;
        border-color: #20c997;
        color: white;
        font-size: 0.8rem;
        padding: 0.3rem 0.8rem;
    }
    .btn-send-sms-custom:hover {
        background-color: #1baa80;
        border-color: #1baa80;
    }
    .search-action-group {
        display: flex;
        align-items: center;
    }
    .search-action-group .search-label {
        font-size: 0.85rem;
        color: #333;
        margin-right: 0.5rem;
        font-weight: 500;
    }
    .search-action-group .form-control-sm {
        font-size: 0.8rem;
        max-width: 180px;
        border: 1px solid #ced4da;
    }

    /* Main Payroll Table */
    .card-datatable.table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table {
        border: 1px solid #dee2e6;
    }
    .payroll-table {
        min-width: 1300px;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
        padding: 0.45rem 0.5rem;
        font-size: 0.8rem;
        border: 1px solid #e9ecef;
        white-space: nowrap;
    }
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 500;
        color: #495057;
    }
    .table td:first-child, .table th:first-child { /* Checkbox column */
        width: 30px;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        position: sticky;
        left: 0;
        z-index: 2;
        background-color: #fff; /* Or match row background */
    }
     .table th:first-child {
        background-color: #f8f9fa; /* Match header bg */
     }
    .form-check-input {
        width: 0.9em;
        height: 0.9em;
        margin-top: 0.1em;
    }
    .payroll-table .employee-id-cell,
    .payroll-table .employee-name-cell {
        font-weight: 500;
        cursor: pointer;
    }
    .payroll-table .employee-id-cell { background-color: #e4f0e5; }
    .sms-status-icon { font-weight: bold; font-size: 1.1em; }
    .sms-status-icon.sent { color: #28a745; }
    .sms-status-icon.failed { color: #dc3545; }
    .sms-status-icon.pending { color: #6c757d; font-size: 1.2em; line-height: 1; }
    .work-days-header::after { content: ' \\25BE'; font-size: 0.7em; vertical-align: middle; margin-left: 4px; color: #6c757d; }
    .payroll-table .col-net_pay { cursor: pointer; color: #007bff; font-weight: bold; }
    .payroll-table .col-net_pay:hover { text-decoration: underline; }
    .table td.col-workdays-data, .table td.col-base_salary, .table td.col-allowances,
    .table td.col-gross_pay, .table td.col-deductions, .table td.col-net_pay {
        text-align: right !important; font-weight: bold; padding-right: 0.8rem;
    }
    .table th.col-workdays, .table th.col-base_salary, .table th.col-allowances,
    .table th.col-gross_pay, .table th.col-deductions, .table th.col-net_pay {
        text-align: right !important; padding-right: 1rem;
    }
    .table td.col-remarks { text-align: left !important; white-space: normal; word-break: break-word; min-width: 150px; }
    .table th.col-remarks { text-align: left !important; }
    .table-active { background-color: rgba(0, 123, 255, 0.08) !important; }

    /* Offcanvas (Attendance/Leave) Styles */
    .offcanvas-header-custom { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6; }
    .offcanvas-title-main { font-size: 1.1rem; font-weight: bold; }
    .offcanvas-title-sub { font-size: 0.9rem; color: #6c757d; margin-top: 0.25rem; }
    .work-days-input-group { display: flex; align-items: center; margin: 1rem 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #eee; }
    .work-days-input-group label { margin-right: 0.5rem; font-size: 0.9rem; white-space: nowrap; }
    .work-days-input-group input { max-width: 80px; text-align: right; font-size: 0.9rem; padding: 0.25rem 0.5rem; }
    .attendance-table-container { padding: 0 1.5rem; margin-bottom: 1rem; }
    .attendance-table th, .attendance-table td { font-size: 0.8rem; padding: 0.4rem; text-align: center; vertical-align: middle; white-space: normal; }
    .attendance-table thead th { background-color: #f8f9fa; }
    .offcanvas-footer-custom { padding: 1rem 1.5rem; border-top: 1px solid #dee2e6; background-color: #f8f9fa; }
    .btn-submit-attendance { width: 100%; }

    /* Offcanvas (Payroll Details) Styles */
    #offcanvasPayrollDetails .offcanvas-header { padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6; align-items: center; }
    #offcanvasPayrollDetails .btn-close-custom { font-size: 1.8rem; font-weight: bold; color: #000; opacity: 0.7; background: transparent; border: none; padding: 0; line-height: 1;}
    #offcanvasPayrollDetails .offcanvas-title { font-size: 1.1rem; font-weight: 600; color: #333; }
    #offcanvasPayrollDetails .offcanvas-body { padding: 1.5rem; font-size: 0.9rem; }
    .payroll-detail-section { margin-bottom: 1.5rem; }
    .payroll-detail-section .section-title { font-size: 1rem; font-weight: bold; color: #333; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; }
    .payroll-detail-section .section-title .total-amount { font-size: 1.1rem; font-weight: bold; }
    .base-salary-item { display: flex; justify-content: space-between; align-items: center; background-color: #e9ecef; padding: 0.6rem 0.8rem; border: 1px solid #ced4da; border-radius: 0.25rem; margin-bottom: 1.5rem; }
    .base-salary-item .label { font-weight: 500; color: #495057; }
    .base-salary-item .amount { font-weight: bold; color: #333; font-size: 0.95rem; }
    .payroll-items-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0rem; } /* For Allowances/Deductions side-by-side */
    .payroll-items-table { width: 100%; border-collapse: collapse; }
    .payroll-items-table td { padding: 0.55rem 0.6rem; font-size: 0.85rem; border: 1px solid #e0e0e0; vertical-align: middle; }
    .payroll-items-table td:first-child { background-color: #f8f9fa; color: #495057; width: 55%; text-align: left; }
    .payroll-items-table td:last-child { text-align: right; font-weight: 500; color: #333; }
    #offcanvasPayrollDetails .offcanvas-footer { padding: 1rem 1.5rem; border-top: 1px solid #dee2e6; background-color: #f8f9fa; }
    #offcanvasPayrollDetails .btn-modify-payroll { background-color: #20c997; border-color: #20c997; color: white; width: 100%; font-size: 0.9rem; padding: 0.5rem; }
    #offcanvasPayrollDetails .btn-modify-payroll:hover { background-color: #1baa80; border-color: #1baa80; }

    /* Offcanvas (SMS Preview & Send) Styles */
    #offcanvasSmsPreview .offcanvas-header { padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6; align-items: center; }
    #offcanvasSmsPreview .btn-close-sms-custom { font-size: 1.8rem; font-weight: bold; color: #000; opacity: 0.7; background: transparent; border: none; padding: 0; line-height: 1;}
    #offcanvasSmsPreview .offcanvas-title { font-size: 1.1rem; font-weight: 600; color: #333; }
    #offcanvasSmsPreview .offcanvas-body { display: flex; padding: 0; height: calc(100% - 57px - 73px); /* Full height minus header and footer for fixed footer */ }
    .sms-offcanvas-left-panel {
        width: 40%; /* As per image */
        padding: 1.2rem;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        background-color: #fdfdfd; /* Slightly off-white */
    }
    .sms-offcanvas-left-panel .selected-info { font-size: 0.9rem; font-weight: bold; margin-bottom: 0.5rem; }
    .sms-offcanvas-left-panel .search-employee-sms input { font-size: 0.8rem; margin-bottom: 0.8rem; }
    .employee-sms-list-container { flex-grow: 1; overflow-y: auto; border: 1px solid #eee; }
    .employee-sms-list-table { width: 100%; font-size: 0.75rem; }
    .employee-sms-list-table th, .employee-sms-list-table td { padding: 0.4rem; text-align: left; border-bottom: 1px solid #f0f0f0; white-space: nowrap; }
    .employee-sms-list-table thead th { background-color: #f8f9fa; font-weight: 500; font-size:0.7rem; }
    .employee-sms-list-table tbody tr { cursor: pointer; }
    .employee-sms-list-table tbody tr:hover { background-color: #f0f8ff; } /* Light blue hover */
    .employee-sms-list-table tbody tr.active-sms-employee { background-color: #ffeeba; font-weight: bold; } /* Yellowish highlight from image */
    .employee-sms-list-table td:nth-child(1) { width: 15%; } /* ID */
    .employee-sms-list-table td:nth-child(2) { width: 30%; } /* Dept */
    .employee-sms-list-table td:nth-child(3) { width: 25%; } /* Name */
    .employee-sms-list-table td:nth-child(4) { width: 30%; } /* Phone */

    .sms-offcanvas-right-panel {
        width: 60%;
        padding: 1rem 1.5rem 0.5rem 1.5rem; /* Adjusted padding */
        display: flex;
        flex-direction: column;
        background-color: #f4f5f7; /* Light grey background for the phone area */
    }
    .phone-preview-container {
        width: 280px; /* Approximate width from image */
        height: 520px; /* Approximate height from image */
        background-color: #fff;
        border-radius: 30px; /* Rounded corners like a phone */
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        margin: 0 auto 1rem auto; /* Center it and add margin bottom */
        padding: 15px;
        display: flex;
        flex-direction: column;
        overflow: hidden; /* Clip content to phone shape */
        position: relative; /* For notch */
    }
    .phone-preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: #333;
        padding: 0 5px 8px 5px; /* Minimal padding */
    }
    .phone-preview-header .time { font-weight: 500; }
    .phone-preview-header .status-icons span { margin-left: 3px; }
    .phone-preview-notch { width: 100px; height: 20px; background-color: #111; border-radius: 0 0 10px 10px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index:10;}
    .phone-preview-contact-bar {
        display: flex;
        align-items: center;
        padding: 8px 0; /* Vertical padding */
        border-bottom: 1px solid #f0f0f0;
        margin-top: 15px; /* Space for notch */
    }
    .phone-preview-contact-bar .back-arrow { font-size: 1.2rem; color: #007aff; margin-right: 10px; }
    .phone-preview-contact-bar .avatar { width: 30px; height: 30px; border-radius: 50%; background-color: #e0e0e0; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; color:#777; margin-right: 8px; }
    .phone-preview-contact-bar .contact-name { font-size: 0.9rem; font-weight: 500; color: #333; }
    .phone-preview-messages {
        flex-grow: 1;
        overflow-y: auto;
        padding: 10px 5px;
        font-size: 0.8rem;
    }
    .phone-preview-messages .sms-timestamp { text-align: center; color: #888; font-size: 0.7rem; margin-bottom: 10px; }
    .sms-bubble {
        background-color: #e9e9eb; /* iOS-like grey bubble */
        padding: 8px 12px;
        border-radius: 15px;
        margin-bottom: 8px;
        max-width: 85%; /* Don't take full width */
        word-wrap: break-word;
        line-height: 1.4;
        color: #000; /* Text color for grey bubble */
    }
    .sms-bubble ul { list-style: none; padding-left: 5px; margin-top: 5px; margin-bottom: 0;}
    .sms-bubble ul li { margin-bottom: 3px; font-size: 0.78rem; }
    .sms-bubble a { color: #007aff; text-decoration: underline; }
    .sms-bubble .sms-item { margin-bottom: 2px; font-size: 0.75rem;}
    .sms-bubble .sms-item .sms-item-label { color: #555;}
    .sms-bubble .sms-item .sms-item-value { font-weight: 500; color: #000;}
    .sms-bubble .sms-total { font-weight: bold; color: #007aff; margin-top:5px;}

    .sms-message-template-area { margin-bottom: 0.8rem; }
    .sms-message-template-area label { font-size: 0.8rem; font-weight: 500; margin-bottom: 0.25rem; display: block; }
    .sms-message-template-area textarea { font-size: 0.75rem; min-height: 60px; border-radius:3px; border: 1px solid #ccc; }
    .sms-info-group { display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; background-color: #e9ecef; padding: 0.5rem 0.8rem; border-radius: 3px; margin-bottom: 0.5rem; }
    .sms-info-group .label { color: #495057; }
    .sms-info-group .value { font-weight: bold; color: #333; }
    #offcanvasSmsPreview .offcanvas-footer {
        padding: 0.8rem 1.5rem;
        border-top: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }
    #offcanvasSmsPreview .btn-resend-sms {
        background-color: #20c997;
        border-color: #20c997;
        color: white;
        width: 100%;
        font-size: 0.9rem;
        padding: 0.5rem;
    }
     #offcanvasSmsPreview .btn-resend-sms:hover {
        background-color: #1baa80;
        border-color: #1baa80;
    }
  </style>
@endsection

@section('vendor-script')
  <script src="{{ asset('templates/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
  <script>
    // Helper function to format numbers with commas
    function formatNumber(num, defaultValue = '-') {
        if (num === null || num === undefined || num === '' || isNaN(Number(num))) return defaultValue;
        return Number(num).toLocaleString('en-US'); // Using 'en-US' for comma separation
    }

    $(function () {
      const payrollDataStore = @json($payrollEntriesForImageDisplay);
      // Add a unique client-side ID to each entry for easier selection tracking
      payrollDataStore.forEach((entry, index) => {
        entry.clientId = `emp-${index}`;
      });

      const payrollTable = $('.payroll-table').DataTable({
        "columnDefs": [ { "targets": 0, "orderable": false, "searchable": false } ],
        "language": {
            "search": "{{ __('datatables.search') }}",
            "searchPlaceholder": "{{ __('datatables.searchPlaceholderShort') }}",
            "info": "", "infoEmpty": "", "infoFiltered": "",
            "zeroRecords": "{{ __('datatables.zeroRecords') }}",
            "paginate": { "first": "{{ __('datatables.paginate.first') }}", "last": "{{ __('datatables.paginate.last') }}", "next": "{{ __('datatables.paginate.next') }}", "previous": "{{ __('datatables.paginate.previous') }}" }
        },
        "lengthChange": false, "info": false, "paging": true, "searching": true,
        "autoWidth": false, "scrollX": false, "dom": 'rt<"bottom"p><"clear">',
        "createdRow": function( row, data, dataIndex ) {
            $(row).attr('data-client-id', payrollDataStore[dataIndex].clientId);
        }
      });

      $('#globalTableSearchInput').on('keyup', function(){ payrollTable.search($(this).val()).draw(); });

      const selectedTextTemplate = "{{ __('payroll.actions.selected_count_display_short', ['count' => ':count_placeholder']) }}";
      function updateSelectedCount() {
          const count = $('.row-checkbox:checked').length;
          let textToShow = selectedTextTemplate.replace(':count_placeholder', count);
          if (count === 0 && selectedTextTemplate.includes(':count_placeholder')) {
             textToShow = "{{ __('payroll.actions.selected_count_none_display', ['count' => 0]) }}";
          } else if (count === 0 && !selectedTextTemplate.includes(':count_placeholder')) {
             textToShow = "{{ __('payroll.actions.selected_count_display_short', ['count' => 0]) }}";
          }
          $('#selectedCountDisplay').text(textToShow);
      }

      $('#selectAllCheckbox').on('click', function() {
          const isChecked = $(this).prop('checked');
          $('.row-checkbox').prop('checked', isChecked).trigger('change');
      });

      $('.payroll-table tbody').on('change', '.row-checkbox', function() {
          $(this).closest('tr').toggleClass('table-active', $(this).prop('checked'));
          if ($('.row-checkbox:checked').length == $('.row-checkbox').length && $('.row-checkbox').length > 0) {
              $('#selectAllCheckbox').prop('checked', true);
          } else {
              $('#selectAllCheckbox').prop('checked', false);
          }
          updateSelectedCount();
      });
      
      $('.payroll-table tbody').on('click', 'tr', function(e) {
        if (!$(e.target).is('input:checkbox') && 
            !$(e.target).closest('.employee-id-cell, .employee-name-cell, .col-net_pay').length) {
            const $checkbox = $(this).find('.row-checkbox');
            $checkbox.prop('checked', !$checkbox.prop('checked')).trigger('change');
        }
      });
      updateSelectedCount();
      $('.row-checkbox:checked').each(function() { $(this).closest('tr').addClass('table-active'); });

      // --- Offcanvas Attendance/Leave Logic ---
      const offcanvasAttendanceLeaveEl = document.getElementById('offcanvasAttendanceLeave');
      const offcanvasAttendanceLeave = new bootstrap.Offcanvas(offcanvasAttendanceLeaveEl);
      $('.payroll-table tbody').on('click', 'td.employee-id-cell, td.employee-name-cell', function() {
          const clientId = $(this).closest('tr').data('client-id');
          const employeeData = payrollDataStore.find(emp => emp.clientId === clientId);
          if (!employeeData) return;
          $('#offcanvasAttendanceLeaveLabelEmployee').text(`${employeeData.id} ${employeeData.name}`);
          $('#offcanvasWorkDaysInput').val(employeeData.work_days);
          
          const attendanceTableBody = $('#attendanceLeaveTableBody');
          attendanceTableBody.empty();
          const exampleDataString = offcanvasAttendanceLeaveEl.getAttribute('data-example-attendance');
          let exampleData = [];
          if (exampleDataString) { try { exampleData = JSON.parse($('<textarea />').html(exampleDataString).text()); } catch (e) { console.error("Error parsing attendance data:", e); attendanceTableBody.append(`<tr><td colspan="5" class="text-center text-danger">{{ __('offcanvas.attendance.data_error') }}</td></tr>`);}}
          if (exampleData && exampleData.length > 0) { exampleData.forEach(item => { attendanceTableBody.append(`<tr><td>${item.type || '-'}</td><td>${item.date || '-'}</td><td>${item.period || '-'}</td><td>${item.paid || '-'}</td><td>${item.memo || '-'}</td></tr>`);});
          } else { attendanceTableBody.append(`<tr><td colspan="5" class="text-center">{{ __('offcanvas.attendance.no_data') }}</td></tr>`); }
          offcanvasAttendanceLeave.show();
      });
      $('#attendanceLeaveForm').on('submit', function(e) { e.preventDefault(); alert('{{ __("offcanvas.attendance.submit_feedback") }}'); offcanvasAttendanceLeave.hide(); });

      // --- Payroll Details Offcanvas Logic ---
      const offcanvasPayrollDetailsEl = document.getElementById('offcanvasPayrollDetails');
      const offcanvasPayrollDetails = new bootstrap.Offcanvas(offcanvasPayrollDetailsEl);
      $('.payroll-table tbody').on('click', 'td.col-net_pay', function() {
          const clientId = $(this).closest('tr').data('client-id');
          const empData = payrollDataStore.find(emp => emp.clientId === clientId);
          if (!empData) return;
          $('#offcanvasPayrollDetailsTitle').text(`${empData.id} ${empData.name} {{ $currentPayrollMonth }} {{ __('payroll.offcanvas_details.title_suffix') }}`);
          $('#payrollDetailBaseSalaryLabel').text(`{{ __('payroll.offcanvas_details.base_salary_label') }}`);
          $('#payrollDetailBaseSalaryAmount').text(formatNumber(empData.numeric_base_salary));
          
          $('#payrollDetailAllowancesTotal').text(formatNumber(empData.numeric_total_allowances));
          const allowancesTableBody = $('#payrollDetailAllowancesTableBody');
          allowancesTableBody.empty();
          if (empData.allowance_items && empData.allowance_items.length > 0) { empData.allowance_items.forEach(item => { allowancesTableBody.append(`<tr><td>${item.label_translation}</td><td class="amount">${formatNumber(item.value)}</td></tr>`); });
          } else { allowancesTableBody.append(`<tr><td colspan="2" class="text-center">- No allowance data -</td></tr>`);}

          $('#payrollDetailDeductionsTotal').text(formatNumber(empData.numeric_total_deductions));
          const deductionsTableBody = $('#payrollDetailDeductionsTableBody');
          deductionsTableBody.empty();
          if (empData.deduction_items && empData.deduction_items.length > 0) { empData.deduction_items.forEach(item => { deductionsTableBody.append(`<tr><td>${item.label_translation}</td><td class="amount">${formatNumber(item.value)}</td></tr>`); });
          } else { deductionsTableBody.append(`<tr><td colspan="2" class="text-center">- No deduction data -</td></tr>`);}
          offcanvasPayrollDetails.show();
      });
      $('#payrollDetailsForm').on('submit', function(e) { e.preventDefault(); alert('"' + '{{ __('payroll.offcanvas_details.modify_button') }}' + '"' + ' clicked. Implement save logic.'); offcanvasPayrollDetails.hide(); });

      // --- SMS Preview Offcanvas Logic ---
      const offcanvasSmsPreviewEl = document.getElementById('offcanvasSmsPreview');
      const offcanvasSmsPreview = new bootstrap.Offcanvas(offcanvasSmsPreviewEl);
      let selectedEmployeesForSms = [];

      function updateSmsPreview(employeeData) {
        if (!employeeData || !employeeData.sms_details) {
            $('#smsPreviewContactName').text('N/A');
            $('#smsPreviewMessageContent').html('<p class="text-muted text-center" style="padding-top: 20px;">Select an employee to see SMS preview.</p>');
            $('#smsMessageTemplateTextarea').val('');
            return;
        }
        const sms = employeeData.sms_details;
        $('#smsPreviewContactName').text(employeeData.name);
        let messageHtml = `<p class="sms-timestamp">{{ __('payroll.offcanvas_sms.sms_timestamp_today') }} ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })}</p>`;
        messageHtml += `<div class="sms-bubble">`;
        messageHtml += `<strong>[${sms.company_name || '{{ __("payroll.offcanvas_sms.sms_company_name") }}'}]</strong> ${sms.intro_line1}<br>${sms.intro_line2}<br>`;
        messageHtml += `<a href="${sms.link_url}" target="_blank">${sms.link_text}</a><br><br>`;
        messageHtml += `<span class="sms-item"><span class="sms-item-label">{{ __('payroll.offcanvas_sms.sms_payment_date_label') }}</span> <span class="sms-item-value">${sms.payment_date}</span></span><br>`;
        messageHtml += `<span class="sms-item"><span class="sms-item-label">{{ __('payroll.offcanvas_sms.sms_emp_code_label') }}</span> <span class="sms-item-value">${employeeData.id}</span></span><br>`;
        messageHtml += `<span class="sms-item"><span class="sms-item-label">{{ __('payroll.offcanvas_sms.sms_emp_name_label') }}</span> <span class="sms-item-value">${employeeData.name} quý vị</span></span><br><br>`; // Added 'quý vị' from image
        messageHtml += `<strong>${sms.statement_title}</strong><br>`;
        messageHtml += `<ul>`;
        sms.earnings.forEach(e => { messageHtml += `<li>${e.label} ${formatNumber(e.value)}</li>`; });
        messageHtml += `</ul>`;
        messageHtml += `<div class="sms-total">${sms.total_gross_pay_label} ${formatNumber(sms.total_gross_pay_value)}</div><br>`;
        messageHtml += `<ul>`; // Added UL for deductions based on image structure
        sms.deductions.forEach(d => { messageHtml += `<li>${d.label} ${formatNumber(d.value)}</li>`; });
        messageHtml += `</ul>`;
        messageHtml += `<div class="sms-total">${sms.total_deductions_label} ${formatNumber(sms.total_deductions_value)}</div>`;
        messageHtml += `</div>`;
        $('#smsPreviewMessageContent').html(messageHtml);

        // Populate textarea (simplified for example)
        let templateText = `[${sms.company_name || '{{ __("payroll.offcanvas_sms.sms_company_name") }}'}] ${sms.intro_line1} ${sms.intro_line2}\n`;
        templateText += `급여명세서 링크 확인하기 ${sms.link_url}\n\n`;
        templateText += `${employeeData.name}님 귀하\n`;
        templateText += `${sms.statement_title}\n`;
        templateText += `지급액계 ${formatNumber(sms.total_gross_pay_value)}\n`;
        templateText += `공제액계 ${formatNumber(sms.total_deductions_value)}`;
        $('#smsMessageTemplateTextarea').val(templateText);
      }

      function populateSmsEmployeeList() {
          const listContainer = $('#employeeSmsListTableBody');
          listContainer.empty();
          $('#smsSelectedEmployeesCount').text("{{ __('payroll.offcanvas_sms.selected_employees_count') }}".replace(':count', selectedEmployeesForSms.length));
          if (selectedEmployeesForSms.length === 0) {
              listContainer.append('<tr><td colspan="4" class="text-center text-muted">No employees selected.</td></tr>');
              updateSmsPreview(null); return;
          }
          selectedEmployeesForSms.forEach((emp, index) => {
              const row = $(`<tr data-client-id="${emp.clientId}"><td>${emp.id}</td><td>${emp.department}</td><td>${emp.name}</td><td>${emp.phone_number || '-'}</td></tr>`);
              if (index === 0) { row.addClass('active-sms-employee'); updateSmsPreview(emp); }
              listContainer.append(row);
          });
      }

      $('.btn-send-sms-custom').on('click', function() {
          selectedEmployeesForSms = [];
          $('.row-checkbox:checked').each(function() {
              const clientId = $(this).closest('tr').data('client-id');
              const employee = payrollDataStore.find(emp => emp.clientId === clientId);
              if (employee) selectedEmployeesForSms.push(employee);
          });
          if (selectedEmployeesForSms.length === 0) { alert("Please select at least one employee to send SMS."); return; }
          populateSmsEmployeeList();
          $('#smsSenderNumberValue').text('070-5555-3333'); // From image
          $('#smsRemainingPointsValue').text(formatNumber(40080)); // From image
          offcanvasSmsPreview.show();
      });

      $('#employeeSmsListTableBody').on('click', 'tr', function() {
          const clientId = $(this).data('client-id');
          if (!clientId) return;
          const employeeData = selectedEmployeesForSms.find(emp => emp.clientId === clientId);
          $('#employeeSmsListTableBody tr').removeClass('active-sms-employee');
          $(this).addClass('active-sms-employee');
          updateSmsPreview(employeeData);
      });

      $('#smsEmployeeSearchInput').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#employeeSmsListTableBody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchTerm));
        });
      });

      $('#smsPreviewForm').on('submit', function(e) {
          e.preventDefault();
          const activeEmployeeRow = $('#employeeSmsListTableBody tr.active-sms-employee');
          if (activeEmployeeRow.length === 0) { alert("Please select an employee from the list."); return; }
          const activeEmployeeClientId = activeEmployeeRow.data('client-id');
          const activeEmployee = selectedEmployeesForSms.find(emp => emp.clientId === activeEmployeeClientId);
          alert(`SMS would be sent/resent to ${activeEmployee.name}. Message: ${$('#smsMessageTemplateTextarea').val()}`);
      });
    });
  </script>
@endsection

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="page-header">
        <div>
            <h1 class="header-main-title mb-0">{{ $image_page_title }}</h1>
            <div class="header-sub-info">
                <span>{{ $image_total_employees_text }}</span>
                <span>{{ $image_sms_sent_employees_text }}</span>
            </div>
        </div>
    </div>

    <div class="excel-upload-area">
        <div class="upload-icon"></div>
        <p>{{ $image_excel_upload_prompt }}</p>
    </div>

    <div class="actions-bar">
        <div class="actions-bar-left">
            <button class="btn btn-action-filter">{{ $image_filter_site_button }}</button>
            <button class="btn btn-action-filter">{{ $image_filter_month_button }}</button>
        </div>
        <div class="actions-bar-right">
            <span id="selectedCountDisplay" class="selected-count-display">{{ $image_selected_count_text }}</span>
            <button class="btn btn-send-sms-custom">{{ $image_send_sms_button_text }}</button>
            <div class="search-action-group">
                 <span class="search-label">{{ $image_search_label }}</span>
                 <input type="text" class="form-control form-control-sm" id="globalTableSearchInput" placeholder="{{ $image_search_placeholder }}">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="payroll-table table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllCheckbox" class="form-check-input" {{ $isSelectAllChecked ? 'checked' : '' }}/></th>
                        <th>{{ __('payroll.table.header.employee_id_short') }}</th>
                        <th>{{ __('payroll.table.header.department') }}</th>
                        <th>{{ __('payroll.table.header.position') }}</th>
                        <th>{{ __('payroll.table.header.name') }}</th>
                        <th class="col-workdays work-days-header">{{ __('payroll.table.header.work_days_short') }}</th>
                        <th class="col-base_salary">{{ __('payroll.table.header.base_salary_short') }}</th>
                        <th class="col-allowances">{{ __('payroll.table.header.allowances_total_short') }}</th>
                        <th class="col-gross_pay">{{ __('payroll.table.header.gross_pay_short') }}</th>
                        <th class="col-deductions">{{ __('payroll.table.header.deductions_total_short') }}</th>
                        <th class="col-net_pay">{{ __('payroll.table.header.net_pay_short') }}</th>
                        <th class="col-remarks">{{ __('payroll.table.header.remarks') }}</th>
                        <th>{{ __('payroll.table.header.sms_sent_status_short') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $displayLimit = 5; $count = 0; @endphp {{-- Limit display for brevity, matching SMS list image --}}
                    @forelse ($payrollEntriesForImageDisplay as $entry)
                        @if($count < $displayLimit)
                        <tr class="{{ ($entry['is_checked'] ?? false) ? 'table-active' : '' }}">
                            <td><input type="checkbox" class="row-checkbox form-check-input" {{ ($entry['is_checked'] ?? false) ? 'checked' : '' }} /></td>
                            <td class="employee-id-cell">{{ $entry['id'] }}</td>
                            <td>{{ $entry['department'] }}</td>
                            <td>{{ $entry['position'] ?? '미화' }}</td>
                            <td class="employee-name-cell">{{ $entry['name'] }}</td>
                            <td class="col-workdays-data">{{ $entry['work_days'] ?? 31 }}</td>
                            <td class="col-base_salary">{{ $entry['base_salary'] ?? '2,299,000' }}</td>
                            <td class="col-allowances">{{ $entry['allowances'] ?? '358,190' }}</td>
                            <td class="col-gross_pay">{{ $entry['gross_pay'] ?? '2,657,190' }}</td>
                            <td class="col-deductions">{{ $entry['deductions'] ?? '358,190' }}</td>
                            <td class="col-net_pay">{{ $entry['net_pay'] ?? '2,366,240' }}</td>
                            <td class="col-remarks">{{ $entry['remarks'] ?? '국민연금 조기수령' }}</td>
                            <td>
                                @if (($entry['sms_sent_status'] ?? 'sent') === 'sent') <span class="sms-status-icon sent">✔</span>
                                @elseif (($entry['sms_sent_status'] ?? 'sent') === 'failed') <span class="sms-status-icon failed">✘</span>
                                @elseif (($entry['sms_sent_status'] ?? 'sent') === 'pending') <span class="sms-status-icon pending">-</span>
                                @else <span class="sms-status-icon pending">-</span> @endif
                            </td>
                        </tr>
                        @php $count++; @endphp
                        @endif
                    @empty
                    <tr><td colspan="13" class="text-center">{{ __('datatables.zeroRecords') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Offcanvas for Attendance/Leave --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAttendanceLeave" aria-labelledby="offcanvasAttendanceLeaveLabel" data-bs-backdrop="static" data-example-attendance="{{ $jsonEncodedAttendanceData }}">
    <div class="offcanvas-header-custom">
        <div>
            <h5 id="offcanvasAttendanceLeaveLabelEmployee" class="offcanvas-title-main mb-0"></h5>
            <div id="offcanvasAttendanceLeaveLabelMonth" class="offcanvas-title-sub">{{ $currentPayrollMonth }} {{ __('offcanvas.attendance.title_suffix') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0 pt-0">
        <form id="attendanceLeaveForm" onsubmit="return false;">
            <div class="work-days-input-group">
                <label for="offcanvasWorkDaysInput" class="form-label mb-0">{{ __('offcanvas.attendance.work_days_input_label') }}</label>
                <input type="number" class="form-control form-control-sm" id="offcanvasWorkDaysInput" name="work_days_override" placeholder="{{ __('offcanvas.attendance.work_days_placeholder') }}">
                <small class="ms-2 text-muted">{{ __('offcanvas.attendance.days_unit') }}</small>
            </div>
            <div class="attendance-table-container">
                <table class="table table-sm table-bordered attendance-table">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('offcanvas.attendance.table.header.type') }}</th>
                            <th>{{ __('offcanvas.attendance.table.header.date') }}</th>
                            <th>{{ __('offcanvas.attendance.table.header.period') }}</th>
                            <th>{{ __('offcanvas.attendance.table.header.paid_status') }}</th>
                            <th>{{ __('offcanvas.attendance.table.header.memo') }}</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceLeaveTableBody"></tbody>
                </table>
            </div>
        </form>
    </div>
    <div class="offcanvas-footer-custom text-center">
        <button type="submit" form="attendanceLeaveForm" class="btn btn-primary btn-submit-attendance">{{ __('offcanvas.attendance.submit_button') }}</button>
    </div>
</div>

{{-- Offcanvas for Payroll Details --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPayrollDetails" aria-labelledby="offcanvasPayrollDetailsLabel" data-bs-backdrop="static" style="width: 450px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasPayrollDetailsTitle"></h5>
        <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body">
        <form id="payrollDetailsForm" onsubmit="return false;">
            <div class="base-salary-item">
                <span id="payrollDetailBaseSalaryLabel" class="label"></span>
                <span id="payrollDetailBaseSalaryAmount" class="amount"></span>
            </div>
            <div class="payroll-items-grid">
                <div class="payroll-detail-section allowances-section">
                    <div class="section-title">
                        <span>{{ __('payroll.offcanvas_details.allowances_section_title') }}</span>
                        <span id="payrollDetailAllowancesTotal" class="total-amount"></span>
                    </div>
                    <table class="payroll-items-table"> <tbody id="payrollDetailAllowancesTableBody"></tbody> </table>
                </div>
                <div class="payroll-detail-section deductions-section">
                    <div class="section-title">
                        <span>{{ __('payroll.offcanvas_details.deductions_section_title') }}</span>
                        <span id="payrollDetailDeductionsTotal" class="total-amount"></span>
                    </div>
                    <table class="payroll-items-table"> <tbody id="payrollDetailDeductionsTableBody"></tbody> </table>
                </div>
            </div>
        </form>
    </div>
    <div class="offcanvas-footer">
        <button type="submit" form="payrollDetailsForm" class="btn btn-modify-payroll">{{ __('payroll.offcanvas_details.modify_button') }}</button>
        // add print button if needed
        <a href="{{ route('payrolls.print') }}" class="btn btn-print-payroll">{{ __('payroll.offcanvas_details.print_button') }}</a>
    </div>
</div>

{{-- Offcanvas for SMS Preview & Send --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSmsPreview" aria-labelledby="offcanvasSmsPreviewLabel" data-bs-backdrop="static" style="width: 750px;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasSmsPreviewLabel">{{ __('payroll.offcanvas_sms.title') }}</h5>
        <button type="button" class="btn-close-sms-custom" data-bs-dismiss="offcanvas" aria-label="Close">X</button>
    </div>
    <div class="offcanvas-body">
        <div class="sms-offcanvas-left-panel">
            <div id="smsSelectedEmployeesCount" class="selected-info"></div>
            <div class="search-employee-sms">
                <input type="text" class="form-control form-control-sm" id="smsEmployeeSearchInput" placeholder="{{ __('payroll.offcanvas_sms.search_placeholder') }}">
            </div>
            <div class="employee-sms-list-container">
                <table class="employee-sms-list-table">
                    <thead>
                        <tr>
                            <th>{{ __('payroll.offcanvas_sms.employee_list_header_id') }}</th>
                            <th>{{ __('payroll.offcanvas_sms.employee_list_header_department') }}</th>
                            <th>{{ __('payroll.offcanvas_sms.employee_list_header_name') }}</th>
                            <th>{{ __('payroll.offcanvas_sms.employee_list_header_phone') }}</th>
                        </tr>
                    </thead>
                    <tbody id="employeeSmsListTableBody"></tbody>
                </table>
            </div>
        </div>
        <div class="sms-offcanvas-right-panel">
            <div class="phone-preview-container">
                <div class="phone-preview-notch"></div>
                <div class="phone-preview-header">
                    <span class="time">9:41</span> <div> <span class="status-icons">📶</span> <span class="status-icons">WiFi</span> <span class="status-icons">🔋</span> </div>
                </div>
                <div class="phone-preview-contact-bar">
                    <span class="back-arrow">ㄑ</span> <span class="avatar">👤</span> <span id="smsPreviewContactName" class="contact-name">{{ __('payroll.offcanvas_sms.sms_recipient_label') }}</span>
                </div>
                <div id="smsPreviewMessageContent" class="phone-preview-messages"></div>
            </div>
            <form id="smsPreviewForm" onsubmit="return false;">
                 <div class="sms-message-template-area">
                    <label for="smsMessageTemplateTextarea">{{ __('payroll.offcanvas_sms.message_template_label') }}</label>
                    <textarea class="form-control" id="smsMessageTemplateTextarea" rows="3"></textarea>
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
    <div class="offcanvas-footer">
        <button type="submit" form="smsPreviewForm" class="btn btn-resend-sms">{{ __('payroll.offcanvas_sms.resend_sms_button') }}</button>
    </div>
</div>

@endsection
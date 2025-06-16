@extends('layouts.app')

@section('title', __('employee.management.page_title'))

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('templates/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
  <style>
    body {
        font-family: Arial, Helvetica, sans-serif; /* Common spreadsheet font */
    }
    .filter-btn-group .btn.active {
        background-color: #007bff; /* Active filter button */
        color: white;
        border-color: #007bff;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .header-main-title {
        font-size: 1.75rem;
        font-weight: bold;
    }
    .excel-upload-area {
      border: 2px dashed #cccccc;
      padding: 25px 20px;
      text-align: center;
      margin-bottom: 20px;
      background-color: #fdfdfd;
      border-radius: 4px;
    }
    .upload-icon {
      width: 40px;
      height: 40px;
      background-color: #e0e0e0;
      margin: 0 auto 10px auto;
      /* Simple icon representation */
      content: "";
      display: block;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23777'%3E%3Cpath d='M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: center;
      background-size: 60%;
    }
    .excel-upload-area p {
        margin-bottom: 0;
        color: #555;
    }
    .action-buttons-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      flex-wrap: wrap;
    }
    .action-buttons-top .left-actions .btn {
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        border-color: #ccc; /* Lighter border for less emphasis */
        color: #333;
        background-color: #f8f9fa; /* Light background like Excel buttons */
    }
     .action-buttons-top .left-actions .btn:hover {
        background-color: #e9ecef;
     }
    .action-buttons-top .right-actions {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .btn-send-sms {
        background-color: #20c997;
        border-color: #20c997;
        color: white;
        padding: 0.375rem 0.75rem;
    }
    .btn-send-sms:hover {
        background-color: #1baa80;
        border-color: #1baa80;
    }
    .btn-setec { /* Assuming 'btn-setec' is from the image (top right) */
        background-color: #f8f9fa;
        border-color: #ccc;
        color: #333;
    }

    /* Excel-like Table Styling */
    .card-datatable {
        border: 1px solid #a0a0a0; /* Outer border for the "sheet" */
    }
    .employee-table {
        border-collapse: collapse; /* Important for Excel look */
        width: 100%;
    }
    .employee-table th,
    .employee-table td {
        border: 1px solid #c0c0c0; /* Grid lines, slightly softer than black */
        padding: 0.4rem 0.5rem;   /* Consistent padding */
        font-size: 0.8rem;     /* Smaller font typical of spreadsheets */
        vertical-align: middle;
        text-align: left;      /* Excel default is left for text, right for numbers */
        white-space: nowrap;   /* Prevent wrapping initially */
    }
    .employee-table thead th {
        background-color: #e9ecef; /* Standard Excel header grey */
        font-weight: bold;         /* Bold headers */
        text-align: center;        /* Headers often centered */
        border-bottom-width: 1px;  /* Slightly thicker bottom border for header */
        border-bottom-color: #a0a0a0;
    }
    .employee-table td:first-child, /* Checkbox column */
    .employee-table th:first-child {
        text-align: center;
        width: 30px; /* Narrower checkbox column */
    }
    .employee-table .employee-id-cell {
        background-color: #f0f8ff; /* Light blue tint for ID, optional */
        text-align: center;
    }
    /* Align numeric-like columns to the right */
    .employee-table td:nth-child(6), /* Age */
    .employee-table td:nth-child(11) /* Base Salary */ {
        text-align: right;
    }
    .employee-table td strong { /* Make salary stand out */
        font-weight: normal; /* Override default strong if needed, or keep for emphasis */
    }

    /* Alternating row colors (optional) */
    /* .employee-table tbody tr:nth-child(even) { background-color: #f9f9f9; } */

    .employee-table tbody tr.table-active td { /* Selected row style */
        background-color: #cfe2ff !important; /* Bootstrap's active blue, or an Excel-like light yellow */
        /* background-color: #fff3cd !important; */ /* Excel-like yellow selection */
    }
    .employee-table tbody tr.table-active td:first-child {
         background-color: #cfe2ff !important; /* Maintain selection color on sticky column */
    }


    .status-badge {
      padding: 0.3em 0.6em;
      font-size: 0.7em; /* Smaller badges */
      font-weight: bold;
      line-height: 1;
      color: #fff;
      text-align: center;
      white-space: nowrap;
      vertical-align: baseline;
      border-radius: 0.2rem;
      display: inline-block;
    }
    .status-badge small {
        display: block;
        font-size: 0.85em;
        font-weight: normal;
        color: inherit;
        margin-top: 2px;
    }
    .status-working { background-color: #28a745; }
    .status-resigning { background-color: #fd7e14; }
    .status-resigned { background-color: #dc3545; }
    .status-on-leave { background-color: #ffc107; color: #212529;}
    .status-on-leave small { color: #212529; }

    .btn-edit {
        background-color: #f8f9fa;
        border: 1px solid #ccc;
        color: #333;
        padding: 0.2rem 0.5rem;
        font-size: 0.75rem; /* Smaller edit button */
        border-radius: 0.2rem;
    }
    .btn-edit:hover {
        background-color: #e9ecef;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .input-group-text {
        font-size: 0.8rem;
        background-color: #e9ecef; /* Match header grey */
        border-color: #c0c0c0;
    }
    .form-control-sm {
        font-size: 0.8rem;
        border-color: #c0c0c0;
    }
    /* DataTables specific overrides for Excel look */
    .dataTables_info, .dataTables_paginate {
        font-size: 0.8rem; /* Smaller info/paginate text */
        padding-top: 0.5rem;
    }
    .dataTables_wrapper .row:nth-child(1) > div:nth-child(1), /* Hide "Show X entries" */
    .dataTables_wrapper .dataTables_filter /* Hide default search if using custom global one */
    {
        /* display: none; */ /* Uncomment if you want to hide DataTable's length and search */
    }
    .dataTables_filter { /* Hide DataTable's default search box */
        display: none;
    }


  </style>
@endsection

@section('vendor-script')
  <script src="{{ asset('templates/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
  <script>
    $(function () {
      const employeeTable = $('.employee-table').DataTable({
        "columnDefs": [ {
            "targets": 0, // First column (checkbox)
            "orderable": false,
            "searchable": false
        }],
        "language": {
            "search": "{{ __('datatables.search') }}", // Used by the global search input if DataTable's search is enabled
            "searchPlaceholder": "{{ __('datatables.searchPlaceholder') }}",
            "lengthMenu": "{{ __('datatables.lengthMenu') }}", // Still provide if lengthChange is true
            "info": "{{ __('datatables.info') }}",
            "infoEmpty": "{{ __('datatables.infoEmpty') }}",
            "infoFiltered": "{{ __('datatables.infoFiltered') }}",
            "zeroRecords": "{{ __('datatables.zeroRecords') }}",
            "paginate": {
                "first": "{{ __('datatables.paginate.first') }}",
                "last": "{{ __('datatables.paginate.last') }}",
                "next": "{{ __('datatables.paginate.next') }}",
                "previous": "{{ __('datatables.paginate.previous') }}"
            }
        },
        "lengthChange": false, // Hide "Show X entries"
        "info": true,       // Show "Showing X to Y of Z entries"
        "paging": true,
        "searching": true, // Keep true so our custom global search works on it
        "autoWidth": false, // Recommended for performance and layout control
        "responsive": false, // Disable DataTables responsive if using custom scroll wrapper for Excel look
        "scrollX": true,    // Enable horizontal scroll for Excel-like behavior
        "dom": 'rt<"bottom-bar d-flex justify-content-between"<"table-info"i><"table-pagination"p>><"clear">' // Custom DOM for bottom elements
        // Remove "l" (length changing) and "f" (filtering input) from dom if fully custom
        // dom: 'rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>' is a common one without search/length
      });

      // Custom global search
      $('#globalTableSearchInput').on('keyup', function(){
          employeeTable.search($(this).val()).draw();
      });

      // Checkbox logic
      const selectedTextTemplate = "{{ __('employee.management.selected_count_display', ['count' => ':count_placeholder']) }}";
      function updateSelectedCount() {
          const count = $('.row-checkbox:checked').length;
          $('#selectedCount').text(selectedTextTemplate.replace(':count_placeholder', count));
      }

      $('#selectAllCheckbox').on('click', function() {
          const isChecked = $(this).prop('checked');
          $('.row-checkbox').prop('checked', isChecked);
          // DataTable API for selection styling if using select extension
          // Otherwise, manual class toggle:
          employeeTable.rows().every(function(){
              $(this.node()).toggleClass('table-active', isChecked);
          });
          updateSelectedCount();
      });

      $('.employee-table tbody').on('click', '.row-checkbox', function() {
          $(this).closest('tr').toggleClass('table-active', $(this).prop('checked'));
          if ($('.row-checkbox:checked').length == employeeTable.rows({ search: 'applied' }).nodes().length) { // Check against visible rows
              $('#selectAllCheckbox').prop('checked', true);
          } else {
              $('#selectAllCheckbox').prop('checked', false);
          }
          updateSelectedCount();
      });

      // Ensure select all checkbox reflects current state on draw (e.g., after search)
      employeeTable.on('draw.dt', function() {
        let allVisibleChecked = true;
        let visibleRows = 0;
        employeeTable.rows({ search: 'applied' }).every(function(){
            visibleRows++;
            if (!$(this.node()).find('.row-checkbox').prop('checked')) {
                allVisibleChecked = false;
            }
        });
        if (visibleRows > 0 && allVisibleChecked) {
            $('#selectAllCheckbox').prop('checked', true);
        } else {
            $('#selectAllCheckbox').prop('checked', false);
        }
        // Re-apply table-active class based on checkbox state
        employeeTable.rows({ search: 'applied' }).nodes().each(function(rowNode){
            $(rowNode).toggleClass('table-active', $(rowNode).find('.row-checkbox').prop('checked'));
        });
        updateSelectedCount();
      });

      updateSelectedCount(); // Initial call
      $('.row-checkbox:checked').each(function() {
        $(this).closest('tr').addClass('table-active');
      });


      // Filter button logic
      $('.filter-btn-group .btn').on('click', function() {
          const $button = $(this);
          const columnIndex = parseInt($button.data('column-index'));
          const filterKeyword = $button.data('filter-keyword');
          const currentSearch = employeeTable.column(columnIndex).search();

          // If this button is active and its filter is already applied, toggle it off
          if ($button.hasClass('active') && currentSearch === filterKeyword) {
              employeeTable.column(columnIndex).search('').draw();
              $button.removeClass('active');
          } else {
              // Deactivate any other 'active' button in this group
              $button.siblings('.btn.active').removeClass('active');
              // Clear any existing search on this column before applying a new one
              employeeTable.column(columnIndex).search(filterKeyword).draw();
              $button.addClass('active');
          }
      });
    });
  </script>
@endsection

@section('content')
@php
    $totalEmployeeCount = 25;
@endphp
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="page-header">
        <div>
            <h1 class="header-main-title mb-0">{{ __('employee.management.page_title') }}</h1>
            <p class="text-muted mb-0">{{ __('employee.management.total_employees_display', ['count' => $totalEmployeeCount]) }}</p>
        </div>
        <div>
            <button class="btn btn-setec btn-sm me-2">{{ __('app.setec_button') }}</button> {{-- Moved SETEC button here --}}
            <button class="btn btn-primary btn-sm"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasEcommerceCustomerAdd"
                    aria-controls="offcanvasEcommerceCustomerAdd">
                <i class="bx bx-plus me-0 me-sm-1"></i>{{ __('employee.management.add_employee') }}
            </button>
        </div>
    </div>

    <div class="excel-upload-area">
        <div class="upload-icon"></div>
        <p>{{ __('employee.management.excel_upload_prompt') }}</p>
    </div>

    <div class="action-buttons-top mb-3">
        <div class="left-actions filter-btn-group btn-group" role="group"> {{-- Added btn-group for better visual grouping --}}
            <button class="btn btn-outline-secondary btn-sm" data-column-index="2" data-filter-keyword="SETEC">
                {{ __('employee.management.filter_by_department') }}
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-column-index="3" data-filter-keyword="미화">
                {{ __('employee.management.filter_by_position') }}
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-column-index="11" data-filter-keyword="{{ __('employee.status.working_plain') ?? '재직' }}">
                {{ __('employee.management.filter_by_status') }}
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-column-index="8" data-filter-keyword="2년 3개월">
                {{ __('employee.management.filter_by_service_period') }}
            </button>
        </div>
        <div class="right-actions">
            <span id="selectedCount" class="me-2 align-self-center" style="font-size: 0.9rem; color: #555;"></span>
            <button class="btn btn-send-sms btn-sm me-2">{{ __('employee.management.send_sms_selected') }}</button>
            <div class="input-group input-group-sm" style="width: 200px;">
                 <span class="input-group-text" id="basic-addon1"><i class="bx bx-search"></i></span>
                 <input type="text" class="form-control form-control-sm" id="globalTableSearchInput" placeholder="{{ __('datatables.searchPlaceholderShort') }}">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive"> {{-- Keep table-responsive for horizontal scroll with DataTable's scrollX --}}
            <table class="employee-table table"> {{-- Removed table-bordered if using Excel-style cell borders --}}
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check-input" id="selectAllCheckbox" /></th>
                        <th>{{ __('employee.table.header.employee_id') }}</th>
                        <th>{{ __('employee.table.header.work_location') }}</th>
                        <th>{{ __('employee.table.header.position') }}</th>
                        <th>{{ __('employee.table.header.name') }}</th>
                        <th>{{ __('employee.table.header.age') }}</th>
                        <th>{{ __('employee.table.header.ssn') }}</th>
                        <th>{{ __('employee.table.header.join_date') }}</th>
                        <th>{{ __('employee.table.header.service_period') }}</th>
                        <th>{{ __('employee.table.header.contact') }}</th>
                        <th>{{ __('employee.table.header.base_salary') }}</th>
                        <th>{{ __('employee.table.header.employment_status') }}</th>
                        <th>{{ __('employee.table.header.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Row 1 --}}
                    <tr>
                        <td><input type="checkbox" class="form-check-input row-checkbox" /></td>
                        <td class="employee-id-cell">586</td>
                        <td>SETEC</td>
                        <td>미화</td>
                        <td>임채정</td>
                        <td>31</td>
                        <td>670502-1******</td> {{-- Masked SSN --}}
                        <td>2024년 3월 21일</td>
                        <td>2년 3개월</td>
                        <td>010-5555-4444</td>
                        <td><strong>2,657,100</strong></td>
                        <td><span class="status-badge status-working">{{ __('employee.status.working_plain') ?? '재직' }}</span></td>
                        <td>
                          <button class="btn btn-edit"
                                    data-bs-toggle="offcanvas"
                              data-bs-target="#offcanvasEcommerceCustomerAdd"
                              aria-controls="offcanvasEcommerceCustomerAdd"
                          >{{ __('app.edit_button') }}</button>
                        </td>
                    </tr>
                    {{-- Row 2 --}}
                    <tr>
                        <td><input type="checkbox" class="form-check-input row-checkbox" /></td>
                        <td class="employee-id-cell">587</td>
                        <td>본사</td>
                        <td>사무</td>
                        <td>김민준</td>
                        <td>35</td>
                        <td>890101-1******</td>
                        <td>2022년 1월 10일</td>
                        <td>1년 5개월</td>
                        <td>010-1234-5678</td>
                        <td><strong>3,200,000</strong></td>
                        <td>
                            <span class="status-badge status-resigning">
                                {{ __('employee.status.resigning_plain') ?? '퇴사예정' }}
                                <small>2025.08.15</small>
                            </span>
                        </td>
                        <td>
                          <button class="btn btn-edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEcommerceCustomerAdd">{{ __('app.edit_button') }}</button>
                        </td>
                    </tr>
                     {{-- Row 3 --}}
                    <tr>
                        <td><input type="checkbox" class="form-check-input row-checkbox" /></td>
                        <td class="employee-id-cell">588</td>
                        <td>SETEC</td>
                        <td>경비</td>
                        <td>박서연</td>
                        <td>28</td>
                        <td>960707-2******</td>
                        <td>2023년 6월 1일</td>
                        <td>0년 10개월</td>
                        <td>010-9876-5432</td>
                        <td><strong>2,400,000</strong></td>
                        <td><span class="status-badge status-working">{{ __('employee.status.working_plain') ?? '재직' }}</span></td>
                        <td>
                          <button class="btn btn-edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEcommerceCustomerAdd">{{ __('app.edit_button') }}</button>
                        </td>
                    </tr>
                     {{-- Row 4 --}}
                    <tr>
                        <td><input type="checkbox" class="form-check-input row-checkbox" /></td>
                        <td class="employee-id-cell">589</td>
                        <td>SETEC</td>
                        <td>미화</td>
                        <td>이도현</td>
                        <td>31</td>
                        <td>670502-1******</td>
                        <td>2024년 3월 21일</td>
                        <td>2년 3개월</td>
                        <td>010-5555-4444</td>
                        <td><strong>2,657,100</strong></td>
                        <td>
                            <span class="status-badge status-on-leave">
                                {{ __('employee.status.on_leave_plain') ?? '휴직' }}
                                <small>2025.05.27 - 2025.05.30</small>
                            </span>
                        </td>
                        <td>
                          <button class="btn btn-edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEcommerceCustomerAdd">{{ __('app.edit_button') }}</button>
                        </td>
                    </tr>
                    {{-- Add more rows as needed to fill up to $totalEmployeeCount or for pagination --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Offcanvas for Add/Edit Employee --}}
<div
    class="offcanvas offcanvas-end"
    tabindex="-1"
    id="offcanvasEcommerceCustomerAdd"
    aria-labelledby="offcanvasEcommerceCustomerAddLabel">
    <div class="offcanvas-header">
      <h5 id="offcanvasEcommerceCustomerAddLabel" class="offcanvas-title">{{ __('employee.management.add_employee_title') }}</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form id="employeeAddForm" onsubmit="return false;">
        {{-- Simplified form fields for brevity, expand as needed --}}
        <div class="mb-3">
          <label class="form-label" for="offcanvas-employee-id">{{ __('employee.table.header.employee_id') }}*</label>
          <input type="text" class="form-control" id="offcanvas-employee-id" name="employee_id" placeholder="586" required />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-name">{{ __('employee.table.header.name') }}*</label>
          <input type="text" class="form-control" id="offcanvas-name" name="name" placeholder="임채정" required />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-work-location">{{ __('employee.table.header.work_location') }}*</label>
          <input type="text" class="form-control" id="offcanvas-work-location" name="work_location" placeholder="SETEC" required />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-position">{{ __('employee.table.header.position') }}*</label>
          <input type="text" class="form-control" id="offcanvas-position" name="position" placeholder="미화" required />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-employment-status">{{ __('employee.table.header.employment_status') }}*</label>
          <select class="form-select" id="offcanvas-employment-status" name="employment_status" required>
            <option value="working" selected>{{ __('employee.status.working_plain') ?? '재직' }}</option>
            <option value="resigning">{{ __('employee.status.resigning_plain') ?? '퇴사예정' }}</option>
            <option value="resigned">{{ __('employee.status.resigned_plain') ?? '퇴사' }}</option>
            <option value="on_leave">{{ __('employee.status.on_leave_plain') ?? '휴직' }}</option>
          </select>
        </div>
        {{-- Add all other fields from your original offcanvas form here --}}
        <div class="pt-3">
          <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('app.save_button') }}</button>
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">{{ __('app.cancel_button') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection
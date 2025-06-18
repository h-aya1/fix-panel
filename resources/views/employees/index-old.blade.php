@extends('layouts.app')

@section('title', __('employee.management.page_title'))

@section('page-style')
  <link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.base.css') }}" type="text/css" />
  <link rel="stylesheet" href="{{ asset('jqwidgets/styles/jqx.bootstrap.css') }}" type="text/css" />
  <style>
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-size: 0.9rem;
    }
    .page-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #dee2e6;
    }
    .header-main-title { font-size: 1.65rem; font-weight: 600; }
    .header-sub-title { font-size: 0.85rem; color: #6c757d; }

    .excel-upload-area {
      border: 2px dashed #adb5bd; padding: 25px 20px; text-align: center;
      margin-bottom: 1.5rem; background-color: #f8f9fa; border-radius: 0.3rem;
    }
    .upload-icon {
      width: 36px; height: 36px; background-color: #dee2e6; margin: 0 auto 12px auto;
      border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #495057;
    }
    .upload-icon::before {
        content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-cloud-arrow-up-fill' viewBox='0 0 16 16'%3E%3Cpath d='M8 2a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 6.095 0 7.555 0 9.318 0 11.366 1.708 13 3.781 13h8.906C14.502 13 16 11.57 16 9.773c0-1.636-1.242-2.969-2.834-3.194C12.923 3.999 10.69 2 8 2zm2.354 5.146a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2z'/%3E%3C/svg%3E");
    }
    .excel-upload-area p { margin-bottom: 0.75rem; color: #495057; font-size: 0.9rem; }

    .action-buttons-top {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;
    }
    .action-buttons-top .btn, .page-header .btn {
        font-size: 0.8rem; padding: 0.4rem 0.8rem; border-radius: 0.25rem;
    }
    .action-buttons-top .left-actions .btn {
        border: 1px solid #ced4da; color: #495057; background-color: #fff;
    }
    .action-buttons-top .left-actions .btn:hover { background-color: #e9ecef; }
    .action-buttons-top .left-actions .btn.active {
        background-color: #0d6efd; color: white; border-color: #0d6efd;
    }
    .btn-primary { background-color: #0d6efd; border-color: #0d6efd; }
    .btn-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
    .btn-send-sms { background-color: #198754; border-color: #198754; color: white; }
    .btn-send-sms:hover { background-color: #157347; border-color: #146c43; }
    .btn-setec { background-color: #e9ecef; border-color: #ced4da; color: #495057; }

    .search-action-group { display: flex; align-items: center; }
    .search-action-group .input-group-text {
        font-size: 0.8rem; background-color: #e9ecef; border-color: #ced4da;
        border-right: none; border-radius: 0.25rem 0 0 0.25rem;
    }
    .search-action-group .form-control-sm {
        font-size: 0.8rem; border-color: #ced4da; max-width: 180px;
        border-left: none; border-radius: 0 0.25rem 0.25rem 0;
    }
    .search-action-group .form-control-sm:focus { box-shadow: none; border-color: #0d6efd; }

    #employeeJqxGrid {
        width: 100%; height: 550px; border: 1px solid #dee2e6;
        border-radius: 0.25rem; overflow: hidden;
    }
    .status-badge-cell {
        display: flex; align-items: center; justify-content: center;
        height: 100%; width: 100%; padding: 0 5px;
    }
    .status-badge {
      padding: 0.35em 0.65em; font-size: 0.75em; font-weight: 600;
      color: #fff; text-align: center; white-space: normal;
      line-height: 1.2; border-radius: 0.25rem; display: inline-block; min-width: 60px;
    }
    .status-badge small { display: block; font-size: 0.8em; font-weight: normal; color: inherit; margin-top: 1px; }
    .status-working { background-color: #198754; }
    .status-resigning { background-color: #fd7e14; }
    .status-resigned { background-color: #dc3545; }
    .status-on-leave { background-color: #ffc107; color: #000; }
    .status-on-leave small { color: #000; }

    .btn-grid-edit {
        font-size: 0.75rem; padding: 0.2rem 0.5rem;
        border: 1px solid #ced4da; background-color: #fff; color: #495057;
        border-radius: 0.2rem;
    }
    .btn-grid-edit:hover { background-color: #e9ecef; }

    #csvPreviewArea .table-responsive { border-radius: 0.25rem; }
    #csvPreviewArea .table th { background-color: #e9ecef; font-weight: 600; }
    #csvPreviewArea .table td, #csvPreviewArea .table th { font-size: 0.8rem; padding: 0.4rem; }

    .offcanvas-header { border-bottom: 1px solid #dee2e6; }
    .offcanvas-title { font-weight: 600; }
  </style>
@endsection

@section('page-script')
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // CRUD AJAX integration
    function fetchEmployees() {
      return $.getJSON("{{ route('employees.index') }}?ajax=1");
    }
    function createEmployee(data) {
      return $.ajax({ url: "{{ route('employees.store') }}", method: 'POST', data: data, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
    }
    function updateEmployee(id, data) {
      return $.ajax({ url: `/employees/${id}`, method: 'PUT', data: data, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
    }
    function deleteEmployee(id) {
      return $.ajax({ url: `/employees/${id}`, method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} });
    }

    // Global function for edit button in grid
    function editEmployee(rowUID) {
        var grid = $("#employeeJqxGrid");
        var rowdata = grid.jqxGrid('getrowdatabyid', rowUID);
        if (rowdata) {
            $('#offcanvas-uid').val(rowdata.uid);
            $('#offcanvas-employee-id').val(rowdata.employee_id);
            $('#offcanvas-work-location').val(rowdata.work_location);
            $('#offcanvas-position').val(rowdata.position);
            $('#offcanvas-name').val(rowdata.name);
            $('#offcanvas-age').val(rowdata.age);
            $('#offcanvas-ssn').val(rowdata.ssn);
            let joinDateToDisplay = rowdata.join_date_str || (rowdata.join_date instanceof Date ? rowdata.join_date.toISOString().split('T')[0] : '');
            $('#offcanvas-join-date').val(joinDateToDisplay);
            $('#offcanvas-join-date-str').val(rowdata.join_date_str);
            $('#offcanvas-service-period').val(rowdata.service_period);
            $('#offcanvas-contact').val(rowdata.contact);
            $('#offcanvas-base-salary').val(rowdata.base_salary);
            $('#offcanvas-employment-status').val(rowdata.employment_status_key).trigger('change');
            $('#offcanvas-employment-status-subtext').val(rowdata.employment_status_subtext);

            $('#offcanvasEcommerceCustomerAddLabel').text("{{ __('employee.management.edit_employee_title') }}");
            $('#employeeAddForm button[type="submit"]').text("{{ __('app.update_button') }}");
            $('#employeeAddForm').attr('data-editing-uid', rowdata.uid);

            var offcanvasElement = document.getElementById('offcanvasEcommerceCustomerAdd');
            var offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (offcanvasInstance) {
                offcanvasInstance.show();
            } else {
                new bootstrap.Offcanvas(offcanvasElement).show();
            }
        } else {
            alert("Error: Could not retrieve employee data for editing.");
        }
    }

    // Helper for SweetAlert
    function showSwal(type, title, text) {
      Swal.fire({
        icon: type,
        title: title,
        text: text,
        customClass: { confirmButton: 'btn btn-primary' },
        buttonsStyling: false
      });
    }

    // Fetch employees from DB and render to grid
    function reloadEmployeeGrid() {
      fetchEmployees().done(function(data) {
        var source = {
          localdata: data,
          datatype: "array",
          id: 'uid',
          datafields: [
            { name: 'uid', type: 'string' },
            { name: 'employee_id', type: 'string' },
            { name: 'work_location', type: 'string' },
            { name: 'position', type: 'string' },
            { name: 'name', type: 'string' },
            { name: 'age', type: 'number' },
            { name: 'ssn', type: 'string' },
            { name: 'join_date', type: 'date' },
            { name: 'join_date_str', type: 'string'},
            { name: 'service_period', type: 'string' },
            { name: 'contact', type: 'string' },
            { name: 'base_salary', type: 'number' },
            { name: 'employment_status_key', type: 'string' },
            { name: 'employment_status_subtext', type: 'string' }
          ]
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        $("#employeeJqxGrid").jqxGrid({ source: dataAdapter });
      });
    }

    // Override form submit for AJAX
    $('#employeeAddForm').off('submit').on('submit', function(e) {
      e.preventDefault();
      var formData = $(this).serialize();
      var editingUid = $(this).attr('data-editing-uid');
      var method = editingUid ? 'PUT' : 'POST';
      var url = editingUid ? `/employees/${editingUid}` : `{{ route('employees.store') }}`;
      $.ajax({
        url: url,
        method: method,
        data: formData,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(res) {
          showSwal('success', 'Success', editingUid ? 'Employee updated.' : 'Employee added.');
          reloadEmployeeGrid();
          bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasEcommerceCustomerAdd')).hide();
        },
        error: function(xhr) {
          let msg = 'Error';
          if (xhr.responseJSON && xhr.responseJSON.errors) {
            msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
          } else if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          showSwal('error', 'Validation Error', msg);
        }
      });
    });

    $(document).ready(function () {
        var employeesData = [
            { uid: "emp_1", selected: false, employee_id: "586", work_location: "SETEC", position: "미화", name: "임채정", age: 31, ssn: "670502-1******", join_date_str: "2024-03-21", service_period: "2년 3개월", contact: "010-5555-4444", base_salary: 2657100, employment_status_key: "working", employment_status_subtext: "" },
            { uid: "emp_2", selected: false, employee_id: "587", work_location: "본사", position: "사무", name: "김민준", age: 35, ssn: "890101-1******", join_date_str: "2022-01-10", service_period: "1년 5개월", contact: "010-1234-5678", base_salary: 3200000, employment_status_key: "resigning", employment_status_subtext: "2025.08.15" },
            { uid: "emp_3", selected: false, employee_id: "588", work_location: "SETEC", position: "경비", name: "박서연", age: 28, ssn: "960707-2******", join_date_str: "2023-06-01", service_period: "0년 10개월", contact: "010-9876-5432", base_salary: 2400000, employment_status_key: "working", employment_status_subtext: "" },
            { uid: "emp_4", selected: false, employee_id: "589", work_location: "SETEC", position: "미화", name: "이도현", age: 31, ssn: "670502-1******", join_date_str: "2024-03-21", service_period: "2년 3개월", contact: "010-5555-4444", base_salary: 2657100, employment_status_key: "on_leave", employment_status_subtext: "2025.05.27 - 2025.05.30" }
        ];
        employeesData.forEach(emp => emp.join_date = emp.join_date_str ? new Date(emp.join_date_str) : null);

        var source = {
            localdata: employeesData,
            datatype: "array",
            id: 'uid',
            datafields: [
                { name: 'uid', type: 'string' }, 
                { name: 'employee_id', type: 'string' }, 
                { name: 'work_location', type: 'string' },
                { name: 'position', type: 'string' }, 
                { name: 'name', type: 'string' },
                { name: 'age', type: 'number' }, 
                { name: 'ssn', type: 'string' },
                { name: 'join_date', type: 'date' }, 
                { name: 'join_date_str', type: 'string'},
                { name: 'service_period', type: 'string' }, 
                { name: 'contact', type: 'string' },
                { name: 'base_salary', type: 'number' },
                { name: 'employment_status_key', type: 'string' },
                { name: 'employment_status_subtext', type: 'string' }
            ]
        };
        var dataAdapter = new $.jqx.dataAdapter(source);

        var statusListSource = [
            { value: "working", label: "{{ __('employee.status.working_plain') ?? '재직' }}" },
            { value: "resigning", label: "{{ __('employee.status.resigning_plain') ?? '퇴사예정' }}" },
            { value: "resigned", label: "{{ __('employee.status.resigned_plain') ?? '퇴사' }}" },
            { value: "on_leave", label: "{{ __('employee.status.on_leave_plain') ?? '휴직' }}" }
        ];

        var statusrenderer = function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {
            let statusKey = rowdata.employment_status_key;
            let subtext = rowdata.employment_status_subtext;
            let statusText = statusKey; let badgeClass = "";
            statusListSource.forEach(function(item){ if(item.value === statusKey) statusText = item.label; });
            switch (statusKey) {
                case 'working': badgeClass = 'status-working'; break;
                case 'resigning': badgeClass = 'status-resigning'; break;
                case 'resigned': badgeClass = 'status-resigned'; break;
                case 'on_leave': badgeClass = 'status-on-leave'; break;
            }
            let subtextHtml = subtext ? `<small>${subtext.replace(' - ', '<br>')}</small>` : "";
            return `<div class="status-badge-cell"><span class="status-badge ${badgeClass}">${statusText}${subtextHtml}</span></div>`;
        };
        var cellsrenderer_currency = function (row, columnfield, value, defaulthtml, columnproperties) {
            if (value == null) return '';
            return '<div style="text-align: right; margin: 5px;">' + parseFloat(value).toLocaleString('ko-KR') + '</div>';
        };

        var initGrid = function() {
            $("#employeeJqxGrid").jqxGrid({
                width: '100%', source: dataAdapter, theme: 'bootstrap',
                pageable: true, pagesizeoptions: ['10', '20', '50', '100'],
                sortable: true, altrows: true, enabletooltips: true,
                editable: false, selectionmode: 'checkbox',
                filterable: true, showfilterrow: true,
                columnsresize: true, columnsreorder: true,
                rendered: function () {
                    var grid = $("#employeeJqxGrid");
                    var checkboxColumnHeader = grid.find(".jqx-grid-column-header:first");
                    if (checkboxColumnHeader.find('#jqxGridSelectAllCheckboxExternal').length === 0) {
                        checkboxColumnHeader.html('');
                        var selectAllContainer = $("<div style='width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;'></div>");
                        var selectAllInput = $("<input type='checkbox' id='jqxGridSelectAllCheckboxExternal' style='margin: 0;' />");
                        selectAllContainer.append(selectAllInput);
                        checkboxColumnHeader.append(selectAllContainer);
                        selectAllInput.on('change', function (event) {
                            if ($(this).is(':checked')) { grid.jqxGrid('selectallrows'); }
                            else { grid.jqxGrid('clearselection'); }
                        });
                    }
                },
                columns: [
                  { text: "UID", datafield: 'uid', width: 120, pinned: true },
                  { text: "{{ __('employee.table.header.employee_id') }}", datafield: 'employee_id', width: 80 },
                  { text: "{{ __('employee.table.header.work_location') }}", datafield: 'work_location', width: 100, filtertype: 'checkedlist' },
                  { text: "{{ __('employee.table.header.position') }}", datafield: 'position', width: 100, filtertype: 'checkedlist' },
                  { text: "{{ __('employee.table.header.name') }}", datafield: 'name', width: 100 },
                  { text: "{{ __('employee.table.header.age') }}", datafield: 'age', width: 60, cellsalign: 'right', filtertype: 'number' },
                  { text: "{{ __('employee.table.header.ssn') }}", datafield: 'ssn', width: 120 },
                  { text: "{{ __('employee.table.header.join_date') }}", datafield: 'join_date', width: 110, cellsformat: 'yyyy-MM-dd', filtertype: 'date' },
                  { text: "join_date_str", datafield: 'join_date_str', width: 110 },
                  { text: "{{ __('employee.table.header.service_period') }}", datafield: 'service_period', width: 100 },
                  { text: "{{ __('employee.table.header.contact') }}", datafield: 'contact', width: 120 },
                  { text: "{{ __('employee.table.header.base_salary') }}", datafield: 'base_salary', width: 110, cellsrenderer: cellsrenderer_currency, filtertype: 'number', cellsalign: 'right' },
                  { text: "{{ __('employee.table.header.employment_status') }}", datafield: 'employment_status_key', width: 140, cellsrenderer: statusrenderer, filtertype: 'checkedlist', filteritems: statusListSource },
                  { text: "{{ __('employee.table.header.employment_status_subtext') }}", datafield: 'employment_status_subtext', width: 140 },
                  { text: "{{ __('employee.table.header.actions') }}", datafield: 'actions', width: 70, sortable: false, filterable: false,
                    cellsrenderer: function (row, column, value, defaulthtml, columnproperties, rowdata) {
                        return `<div style="text-align: center; margin-top: 2px;"><button class="btn btn-grid-edit btn-sm" onclick="editEmployee('${rowdata.uid}')">{{ __("app.edit_button") }}</button></div>`;
                    }
                  }
                ]
            });
        }
        initGrid();

        const selectedTextTemplate = "{{ __('employee.management.selected_count_display', ['count' => ':count_placeholder']) }}";
        function updateSelectedCountDisplay() {
            var selectedrowindexes = $('#employeeJqxGrid').jqxGrid('getselectedrowindexes');
            $('#selectedCount').text(selectedTextTemplate.replace(':count_placeholder', selectedrowindexes.length));
        }

        $('#employeeJqxGrid').on('rowselect rowunselect bindingcomplete', function (event) {
            updateSelectedCountDisplay();
            var grid = $("#employeeJqxGrid");
            var selection = grid.jqxGrid('getselectedrowindexes');
            var allBoundRowsCount = grid.jqxGrid('getboundrows').length;
            var selectAllCheckbox = $('#jqxGridSelectAllCheckboxExternal');
            if (!selectAllCheckbox.length) return;

            if (allBoundRowsCount === 0) {
                selectAllCheckbox.prop('checked', false); selectAllCheckbox.prop('indeterminate', false); return;
            }
            let visibleSelectedCount = 0;
            for(let i = 0; i < allBoundRowsCount; i++) {
                if (grid.jqxGrid('isrowselected', grid.jqxGrid('getrowboundindex', i))) { visibleSelectedCount++; }
            }
            if (visibleSelectedCount === allBoundRowsCount) {
                 selectAllCheckbox.prop('checked', true); selectAllCheckbox.prop('indeterminate', false);
            } else if (visibleSelectedCount > 0) {
                 selectAllCheckbox.prop('checked', false); selectAllCheckbox.prop('indeterminate', true);
            } else {
                 selectAllCheckbox.prop('checked', false); selectAllCheckbox.prop('indeterminate', false);
            }
        });
        updateSelectedCountDisplay();

        $('#globalTableSearchInput').on('keyup', function () {
            var searchValue = $(this).val();
            var grid = $("#employeeJqxGrid");
            grid.jqxGrid('clearfilters');
            if (searchValue) {
                var filtergroup = new $.jqx.filter();
                var operator = 0;
                var stringColumns = ['employee_id', 'work_location', 'position', 'name', 'ssn', 'service_period', 'contact'];
                stringColumns.forEach(function(colName) {
                    var filter = filtergroup.createfilter('stringfilter', searchValue, 'CONTAINS');
                    filtergroup.addfilter(operator, filter);
                });
                stringColumns.forEach(function(colName) { grid.jqxGrid('addfilter', colName, filtergroup, false); });
                grid.jqxGrid('applyfilters');
            }
        });

        $('.filter-btn-group .btn').on('click', function() {
            var $button = $(this); var datafield = $button.data('datafield'); var filtervalue = $button.data('filter-value');
            var grid = $("#employeeJqxGrid");
            grid.jqxGrid('removefilter', datafield, false);
            if ($button.hasClass('active')) {
                $button.removeClass('active');
            } else {
                $button.siblings('.btn.active[data-datafield="'+datafield+'"]').removeClass('active');
                var filtergroup = new $.jqx.filter();
                var filter = filtergroup.createfilter('stringfilter', filtervalue, 'EQUAL');
                filtergroup.addfilter(0, filter);
                grid.jqxGrid('addfilter', datafield, filtergroup, false);
                $button.addClass('active');
            }
            grid.jqxGrid('applyfilters');
        });

        $('#excelFile').on('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                $('#csvPreviewFileName').text("{{ __('employee.management.previewing_file') }}: " + file.name);
                if (file.name.endsWith('.csv')) { parseAndDisplayCsv(file); }
                else {
                    alert("{{ __('employee.management.excel_to_csv_conversion_note') }}");
                    $('#csvPreviewArea').hide(); $(this).val('');
                }
            }
        });
        function parseAndDisplayCsv(file) {
            Papa.parse(file, { header: true, skipEmptyLines: true, dynamicTyping: true,
                complete: function(results) {
                    if (results.errors.length > 0) {
                        console.error("CSV Parsing Errors:", results.errors);
                        alert("{{ __('employee.management.csv_parse_error') }}");
                        $('#csvPreviewArea').hide(); return;
                    }
                    displayCsvData(results.data, results.meta.fields);
                }
            });
        }
        function displayCsvData(data, headers) {
            const previewTableHead = $('#csvPreviewTable thead');
            const previewTableBody = $('#csvPreviewTable tbody');
            previewTableHead.empty(); previewTableBody.empty();

            if (!data || data.length === 0) {
                let colspan = headers ? headers.length : 1;
                previewTableBody.append(`<tr><td colspan="${colspan}">{{ __('employee.management.empty_file_preview') }}</td></tr>`);
                $('#csvPreviewArea').show(); return;
            }
            let headerRow = '<tr>';
            const effectiveHeaders = headers || Object.keys(data[0] || {});
            effectiveHeaders.forEach(header => { headerRow += `<th>${header}</th>`; });
            headerRow += '</tr>';
            previewTableHead.append(headerRow);

            const previewLimit = 10;
            data.slice(0, previewLimit).forEach(row => {
                let bodyRow = '<tr>';
                effectiveHeaders.forEach(header => {
                    bodyRow += `<td>${row[header] != null ? row[header] : ''}</td>`;
                });
                bodyRow += '</tr>';
                previewTableBody.append(bodyRow);
            });
            if (data.length > previewLimit) {
                let colspan = effectiveHeaders.length > 0 ? effectiveHeaders.length : 1;
                let remainingRows = data.length - previewLimit;
                let moreRowsText = "{{ __('employee.management.and_more_rows_placeholder') }}";
                moreRowsText = moreRowsText.replace(':count', remainingRows);
                previewTableBody.append(`<tr><td colspan="${colspan}" class="text-muted text-center">... ${moreRowsText} ...</td></tr>`);
            }
            $('#csvPreviewArea').data('parsedData', data);
            $('#csvPreviewArea').show();
        }

        $('#cancelCsvPreviewBtn').on('click', function() {
            $('#csvPreviewArea').hide(); $('#csvPreviewTable tbody').empty();
            $('#csvPreviewTable thead').empty(); $('#excelFile').val('');
        });

        $('#importCsvDataBtn').on('click', function() {
            const importedData = $('#csvPreviewArea').data('parsedData');
            if (importedData && importedData.length > 0) {
                const mappedData = importedData.map(row => {
                    let newUid = 'imported_' + Math.random().toString(36).substr(2, 9);
                    return {
                        uid: newUid, selected: false,
                        employee_id: row['EmpID'] || row['Employee ID'] || null,
                        name: row['FullName'] || row['Name'] || null,
                        work_location: row['WorkSite'] || row['Department'] || null,
                        position: row['JobTitle'] || row['Position'] || null,
                        age: row['Age'] ? parseInt(row['Age']) : null,
                        ssn: row['SSN'] || null,
                        join_date_str: row['HireDate (YYYY-MM-DD)'] || null,
                        join_date: (row['HireDate (YYYY-MM-DD)']) ? new Date(row['HireDate (YYYY-MM-DD)']) : null,
                        service_period: row['ServicePeriod'] || null,
                        contact: row['ContactNumber'] || row['Phone'] || null,
                        base_salary: row['BaseSalary'] ? parseFloat(String(row['BaseSalary']).replace(/,/g, '')) : null,
                        employment_status_key: row['StatusKey'] || row['Employment Status'] || 'working',
                        employment_status_subtext: row['StatusSubtext'] || ''
                    };
                }).filter(row => row.employee_id && row.name);

                if (mappedData.length > 0) {
                    // To prevent issues if adding many rows, batch or use beginupdate/endupdate
                    $("#employeeJqxGrid").jqxGrid('beginupdate');
                    mappedData.forEach(newRow => { $("#employeeJqxGrid").jqxGrid('addrow', newRow.uid, newRow); });
                    $("#employeeJqxGrid").jqxGrid('endupdate');
                    alert(mappedData.length + " {{ __('employee.management.records_imported_successfully') }}");
                } else { alert("{{ __('employee.management.no_valid_data_to_import') }}"); }
                $('#csvPreviewArea').hide(); $('#excelFile').val('');
            } else { alert("{{ __('employee.management.no_data_to_import') }}"); }
        });

        $('#employeeAddForm').on('submit', function(e) {
            e.preventDefault();
            var editingUid = $(this).attr('data-editing-uid');
            var joinDateStr = $('#offcanvas-join-date').val();
            var employeeData = {
                uid: $('#offcanvas-uid').val() || ('new_' + Math.random().toString(36).substr(2, 9)),
                employee_id: $('#offcanvas-employee-id').val(),
                work_location: $('#offcanvas-work-location').val(),
                position: $('#offcanvas-position').val(),
                name: $('#offcanvas-name').val(),
                age: parseInt($('#offcanvas-age').val()) || null,
                ssn: $('#offcanvas-ssn').val(),
                join_date: joinDateStr ? new Date(joinDateStr) : null,
                join_date_str: $('#offcanvas-join-date-str').val() || joinDateStr,
                service_period: $('#offcanvas-service-period').val(),
                contact: $('#offcanvas-contact').val(),
                base_salary: parseFloat($('#offcanvas-base-salary').val()) || null,
                employment_status_key: $('#offcanvas-employment-status').val(),
                employment_status_subtext: $('#offcanvas-employment-status-subtext').val(),
            };

            if (editingUid) {
                updateEmployee(editingUid, employeeData).done(function() {
                    $("#employeeJqxGrid").jqxGrid('updaterow', editingUid, employeeData);
                    alert("{{ __('employee.management.update_success') }}");
                    $(this).removeAttr('data-editing-uid');
                }).fail(function(jqXHR) {
                    alert("{{ __('employee.management.update_failed') }}: " + jqXHR.responseJSON.message);
                });
            } else {
                createEmployee(employeeData).done(function(response) {
                    $("#employeeJqxGrid").jqxGrid('addrow', response.uid, response, 'first');
                    alert("{{ __('employee.management.add_success') }}");
                }).fail(function(jqXHR) {
                    alert("{{ __('employee.management.add_failed') }}: " + jqXHR.responseJSON.message);
                });
            }
            bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasEcommerceCustomerAdd')).hide();
        });

        $('#offcanvasEcommerceCustomerAdd').on('hidden.bs.offcanvas', function () {
             $('#employeeAddForm')[0].reset();
             $('#employeeAddForm').removeAttr('data-editing-uid');
             $('#offcanvasEcommerceCustomerAddLabel').text("{{ __('employee.management.add_employee_title') }}");
             $('#employeeAddForm button[type="submit"]').text("{{ __('app.add_button') }}");
             $('#employment-status-subtext-group').hide();
        });

        $('#offcanvas-employment-status').on('change', function() {
            var status = $(this).val();
            var subtextGroup = $('#employment-status-subtext-group');
            var subtextLabel = $('#employment-status-subtext-label');
            var subtextInput = $('#offcanvas-employment-status-subtext');
            if (status === 'resigning') {
                subtextLabel.text("{{ __('employee.offcanvas.resignation_date_label') }}");
                subtextInput.attr('placeholder', "YYYY-MM-DD"); subtextGroup.show();
            } else if (status === 'on_leave') {
                subtextLabel.text("{{ __('employee.offcanvas.leave_period_label') }}");
                subtextInput.attr('placeholder', "YYYY-MM-DD - YYYY-MM-DD"); subtextGroup.show();
            } else { subtextGroup.hide(); subtextInput.val(''); }
        }).trigger('change');

        $('#downloadTemplateBtn').on('click', function() {
            var csvHeaders = ["EmpID", "FullName", "WorkSite", "JobTitle", "Age", "SSN", "HireDate (YYYY-MM-DD)", "ContactNumber", "BaseSalary (Numbers only)", "StatusKey (working/resigning/resigned/on_leave)", "StatusSubtext (e.g., Resignation Date or Leave Period)"];
            var csvContent = csvHeaders.join(",") + "\n";
            var blob = new Blob(["\ufeff" + csvContent], { type: 'text/csv;charset=utf-8;' }); // Added BOM for Excel UTF-8
            var link = document.createElement("a");
            if (link.download !== undefined) {
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url); link.setAttribute("download", "employee_upload_template.csv");
                link.style.visibility = 'hidden'; document.body.appendChild(link);
                link.click(); document.body.removeChild(link);
            } else { alert("{{ __('employee.management.browser_not_support_download') }}"); }
        });
    });

    reloadEmployeeGrid()
  </script>
@endsection

@section('content')
@php
    $totalEmployeeCount = 25;
@endphp
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="page-header">
        <div>
            <h1 class="header-main-title mb-1">{{ __('employee.management.page_title') }}</h1>
            <p class="header-sub-title mb-0">{{ __('employee.management.total_employees_display', ['count' => $totalEmployeeCount]) }}</p>
        </div>
        <div>
            <button class="btn btn-setec btn-sm me-2">{{ __('app.setec_button') }}</button>
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
        <input type="file" id="excelFile" accept=".csv" style="display: none;" />
        <button type="button" class="btn btn-outline-primary btn-sm mt-2 me-2" onclick="document.getElementById('excelFile').click();">
            <i class="bx bx-file-find me-1"></i>{{ __('employee.management.browse_excel_button') }}
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="downloadTemplateBtn">
            <i class="bx bx-download me-1"></i>{{ __('employee.management.download_template_button') }}
        </button>
    </div>

    <div id="csvPreviewArea" style="display: none;" class="card mt-3 mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 id="csvPreviewFileName" class="mb-0 card-title"></h5>
            <div>
                 <button type="button" class="btn btn-success btn-sm me-2" id="importCsvDataBtn"><i class="bx bx-import me-1"></i>{{ __('employee.management.import_data_button') }}</button>
                 <button type="button" class="btn btn-danger btn-sm" id="cancelCsvPreviewBtn"><i class="bx bx-x me-1"></i>{{ __('app.cancel_button') }}</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 350px;">
                <table class="table table-bordered table-sm table-hover" id="csvPreviewTable">
                    <thead></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="action-buttons-top mb-3">
        <div class="left-actions filter-btn-group btn-group" role="group">
            <button class="btn btn-outline-secondary btn-sm" data-datafield="work_location" data-filter-value="SETEC">
                {{ __('employee.management.filter_by_department') }}
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-datafield="position" data-filter-value="미화">
                {{ __('employee.management.filter_by_position') }}
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-datafield="employment_status_key" data-filter-value="working">
                {{ __('employee.management.filter_by_status') }}
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-datafield="service_period" data-filter-value="2년 3개월">
                {{ __('employee.management.filter_by_service_period') }}
            </button>
        </div>
        <div class="right-actions">
            <span id="selectedCount" class="me-2 align-self-center" style="font-size: 0.9rem; color: #495057;"></span>
            <button class="btn btn-send-sms btn-sm me-2"><i class="bx bx-mail-send me-1"></i>{{ __('employee.management.send_sms_selected') }}</button>
            <div class="input-group input-group-sm search-action-group" style="width: 220px;">
                 <span class="input-group-text"><i class="bx bx-search"></i></span>
                 <input type="text" class="form-control form-control-sm" id="globalTableSearchInput" placeholder="{{ __('datatables.searchPlaceholderShort') }}">
            </div>
        </div>
    </div>

    <div id="employeeJqxGrid"></div>

</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEcommerceCustomerAdd" aria-labelledby="offcanvasEcommerceCustomerAddLabel" data-bs-backdrop="static">
    <div class="offcanvas-header">
      <h5 id="offcanvasEcommerceCustomerAddLabel" class="offcanvas-title">{{ __('employee.management.add_employee_title') }}</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
      <form id="employeeAddForm" onsubmit="return false;">
        <input type="hidden" id="offcanvas-uid" name="uid" />
        <div class="mb-3">
          <label class="form-label" for="offcanvas-employee-id">{{ __('employee.table.header.employee_id') }}*</label>
          <input type="text" class="form-control form-control-sm" id="offcanvas-employee-id" name="employee_id" required />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-work-location">{{ __('employee.table.header.work_location') }}*</label>
          <input type="text" class="form-control form-control-sm" id="offcanvas-work-location" name="work_location" required />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-position">{{ __('employee.table.header.position') }}*</label>
          <input type="text" class="form-control form-control-sm" id="offcanvas-position" name="position" required />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-name">{{ __('employee.table.header.name') }}*</label>
          <input type="text" class="form-control form-control-sm" id="offcanvas-name" name="name" required />
        </div>
        <div class="row gx-2 mb-3">
          <div class="col-md-6">
            <label class="form-label" for="offcanvas-age">{{ __('employee.table.header.age') }}</label>
            <input type="number" class="form-control form-control-sm" id="offcanvas-age" name="age" />
          </div>
          <div class="col-md-6">
            <label class="form-label" for="offcanvas-ssn">{{ __('employee.table.header.ssn') }}</label>
            <input type="text" class="form-control form-control-sm" id="offcanvas-ssn" name="ssn" placeholder="YYMMDD-N******"/>
          </div>
        </div>
        <div class="row gx-2 mb-3">
          <div class="col-md-6">
            <label class="form-label" for="offcanvas-join-date">{{ __('employee.table.header.join_date') }}*</label>
            <input type="date" class="form-control form-control-sm" id="offcanvas-join-date" name="join_date" required />
          </div>
          <div class="col-md-6">
            <label class="form-label" for="offcanvas-join-date-str">join_date_str</label>
            <input type="text" class="form-control form-control-sm" id="offcanvas-join-date-str" name="join_date_str" />
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-service-period">{{ __('employee.table.header.service_period') }}</label>
          <input type="text" class="form-control form-control-sm" id="offcanvas-service-period" name="service_period" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-contact">{{ __('employee.table.header.contact') }}</label>
          <input type="tel" class="form-control form-control-sm" id="offcanvas-contact" name="contact" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-base-salary">{{ __('employee.table.header.base_salary') }}</label>
          <input type="number" class="form-control form-control-sm" id="offcanvas-base-salary" name="base_salary" step="1000" />
        </div>
        <div class="mb-3">
          <label class="form-label" for="offcanvas-employment-status">{{ __('employee.table.header.employment_status') }}*</label>
          <select class="form-select form-select-sm" id="offcanvas-employment-status" name="employment_status_key" required>
            <option value="working" selected>{{ __('employee.status.working_plain') ?? '재직' }}</option>
            <option value="resigning">{{ __('employee.status.resigning_plain') ?? '퇴사예정' }}</option>
            <option value="resigned">{{ __('employee.status.resigned_plain') ?? '퇴사' }}</option>
            <option value="on_leave">{{ __('employee.status.on_leave_plain') ?? '휴직' }}</option>
          </select>
        </div>
        <div class="mb-3" id="employment-status-subtext-group" style="display:none;">
          <label class="form-label" for="offcanvas-employment-status-subtext" id="employment-status-subtext-label"></label>
          <input type="text" class="form-control form-control-sm" id="offcanvas-employment-status-subtext" name="employment_status_subtext" />
        </div>
        <div class="pt-3 d-flex justify-content-end">
          <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="offcanvas">{{ __('app.cancel_button') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('app.add_button') }}</button>
        </div>
      </form>
    </div>
  </div>
@endsection